<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthCheck;
use App\Services\AuditService;
use App\Services\Nav\NavMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class HealthCheckController extends Controller
{
    public function index()
    {
        $checks = HealthCheck::latestByType()->latest('checked_at')->get();
        $allChecks = HealthCheck::latest()->take(50)->get();
        return view('admin.health.index', compact('checks', 'allChecks'));
    }

    public function run()
    {
        $results = [];

        $results[] = $this->checkDatabase();
        $results[] = $this->checkSite();
        $results[] = $this->checkServer();
        $results[] = $this->checkEmailServer();

        AuditService::activity('health_checks_run', 'Platform health checks executed');

        return back()->with('success', 'Health checks completed.');
    }

    protected function checkDatabase(): HealthCheck
    {
        try {
            DB::select('SELECT 1');
            return HealthCheck::create([
                'type' => 'database',
                'status' => 'up',
                'message' => 'Database connection is healthy',
                'details' => ['driver' => config('database.default')],
                'checked_at' => now(),
            ]);
        } catch (\Exception $e) {
            return HealthCheck::create([
                'type' => 'database',
                'status' => 'down',
                'message' => 'Database connection failed: ' . $e->getMessage(),
                'details' => ['error' => $e->getMessage()],
                'checked_at' => now(),
            ]);
        }
    }

    protected function checkSite(): HealthCheck
    {
        try {
            $url = config('app.url');
            $start = microtime(true);
            $response = Http::timeout(10)->get($url);
            $elapsed = round((microtime(true) - $start) * 1000);

            return HealthCheck::create([
                'type' => 'site',
                'status' => $response->successful() ? 'up' : 'degraded',
                'message' => $response->successful()
                    ? "Site responded in {$elapsed}ms"
                    : "Site returned status {$response->status()} in {$elapsed}ms",
                'details' => [
                    'url' => $url,
                    'status_code' => $response->status(),
                    'response_time_ms' => $elapsed,
                ],
                'checked_at' => now(),
            ]);
        } catch (\Exception $e) {
            return HealthCheck::create([
                'type' => 'site',
                'status' => 'down',
                'message' => 'Site unreachable: ' . $e->getMessage(),
                'details' => ['error' => $e->getMessage()],
                'checked_at' => now(),
            ]);
        }
    }

    protected function checkServer(): HealthCheck
    {
        try {
            $diskTotal = disk_total_space(base_path());
            $diskFree = disk_free_space(base_path());
            $diskUsed = $diskTotal - $diskFree;
            $diskPercent = $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100) : 0;
            $phpVersion = PHP_VERSION;
            $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'N/A';

            $status = $diskPercent < 90 ? 'up' : 'degraded';
            $message = $diskPercent < 90
                ? "Disk usage: {$diskPercent}%, PHP {$phpVersion}"
                : "Disk usage critical: {$diskPercent}%";

            return HealthCheck::create([
                'type' => 'server',
                'status' => $status,
                'message' => $message,
                'details' => [
                    'disk_usage_percent' => $diskPercent,
                    'disk_total_gb' => round($diskTotal / 1073741824, 1),
                    'disk_free_gb' => round($diskFree / 1073741824, 1),
                    'php_version' => $phpVersion,
                    'server_software' => $serverSoftware,
                    'laravel_version' => app()->version(),
                ],
                'checked_at' => now(),
            ]);
        } catch (\Exception $e) {
            return HealthCheck::create([
                'type' => 'server',
                'status' => 'down',
                'message' => 'Server check failed: ' . $e->getMessage(),
                'details' => ['error' => $e->getMessage()],
                'checked_at' => now(),
            ]);
        }
    }

    /**
     * Verifies the NAV/Business Central mail WSDL is reachable and NTLM
     * authentication succeeds — the email server that actually sends our
     * outgoing mail (underwriter notifications, quotation emails, etc.)
     * once NAV_MAIL_ENABLED is turned on. Never triggers a real send.
     */
    protected function checkEmailServer(): HealthCheck
    {
        $wsdl = config('services.nav.mail_wsdl');

        if (!$wsdl) {
            return HealthCheck::create([
                'type' => 'email_server',
                'status' => 'down',
                'message' => 'NAV mail WSDL is not configured (NAV_MAIL_WSDL).',
                'details' => [],
                'checked_at' => now(),
            ]);
        }

        $start = microtime(true);
        $result = app(NavMailService::class)->testConnection();
        $elapsed = round((microtime(true) - $start) * 1000);

        return HealthCheck::create([
            'type' => 'email_server',
            'status' => $result['success'] ? 'up' : 'down',
            'message' => $result['success']
                ? "Email server (NAV) reachable, NTLM auth OK ({$elapsed}ms)"
                : "Email server (NAV): {$result['message']}",
            'details' => [
                'url' => $wsdl,
                'enabled' => (bool) config('services.nav.enabled'),
                'response_time_ms' => $elapsed,
            ],
            'checked_at' => now(),
        ]);
    }
}
