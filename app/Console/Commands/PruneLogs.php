<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\AuditLog;
use Illuminate\Console\Command;

class PruneLogs extends Command
{
    protected $signature = 'logs:prune {--days=30 : Retention period in days (default 30 = one month)}';

    protected $description = 'Delete activity logs and audit-trail entries older than the retention period (default: 1 month)';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $cutoff = now()->subDays($days);

        $activities = ActivityLog::where('created_at', '<', $cutoff)->delete();
        $audits = AuditLog::where('created_at', '<', $cutoff)->delete();

        $this->info("Pruned {$activities} activity log(s) and {$audits} audit log(s) older than {$days} day(s) (before {$cutoff->toDateTimeString()}).");

        return self::SUCCESS;
    }
}
