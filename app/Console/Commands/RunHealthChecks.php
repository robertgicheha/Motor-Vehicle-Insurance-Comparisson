<?php

namespace App\Console\Commands;

use App\Models\HealthCheck;
use App\Models\User;
use App\Notifications\SystemHealthAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

/**
 * Scheduled uptime monitoring — records DB/site/server status on a timer
 * (see routes/console.php) rather than only when an admin happens to log in,
 * and alerts admins the moment something goes down.
 */
class RunHealthChecks extends Command
{
    protected $signature = 'health:check';

    protected $description = 'Run scheduled uptime checks (database, site, server disk) and alert admins on failure';

    public function handle(): int
    {
        $this->checkDatabase();
        $this->checkSite();
        $this->checkServer();

        return self::SUCCESS;
    }

    protected function checkDatabase(): void
    {
        try {
            DB::select('SELECT 1');
            HealthCheck::create(['type' => 'database', 'status' => 'up', 'message' => 'Database connection OK', 'checked_at' => now()]);
        } catch (\Exception $e) {
            HealthCheck::create(['type' => 'database', 'status' => 'down', 'message' => $e->getMessage(), 'checked_at' => now()]);
            $this->notifyAdmins('database', $e->getMessage());
        }
    }

    protected function checkSite(): void
    {
        try {
            $start = microtime(true);
            $response = (new \Illuminate\Http\Client\Factory)->timeout(10)->get(config('app.url'));
            $time = round((microtime(true) - $start) * 1000);
            $status = $response->successful() ? 'up' : ($response->serverError() ? 'down' : 'degraded');
            $message = "{$response->status()} in {$time}ms";
            HealthCheck::create(['type' => 'site', 'status' => $status, 'message' => $message, 'checked_at' => now()]);
            if ($status === 'down') {
                $this->notifyAdmins('site', $message);
            }
        } catch (\Exception $e) {
            HealthCheck::create(['type' => 'site', 'status' => 'down', 'message' => $e->getMessage(), 'checked_at' => now()]);
            $this->notifyAdmins('site', $e->getMessage());
        }
    }

    protected function checkServer(): void
    {
        try {
            $total = disk_total_space(base_path());
            $free = disk_free_space(base_path());
            $usedPct = round((1 - $free / $total) * 100);
            $status = $usedPct < 90 ? 'up' : 'degraded';
            $message = "Disk {$usedPct}% used";
            HealthCheck::create(['type' => 'server', 'status' => $status, 'message' => $message, 'checked_at' => now()]);
            if ($status === 'degraded' && $usedPct >= 95) {
                $this->notifyAdmins('server', $message);
            }
        } catch (\Exception $e) {
            HealthCheck::create(['type' => 'server', 'status' => 'down', 'message' => $e->getMessage(), 'checked_at' => now()]);
            $this->notifyAdmins('server', $e->getMessage());
        }
    }

    protected function notifyAdmins(string $type, string $message): void
    {
        $admins = User::role(['super-admin', 'admin'])->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new SystemHealthAlert($type, $message));
        }
    }
}
