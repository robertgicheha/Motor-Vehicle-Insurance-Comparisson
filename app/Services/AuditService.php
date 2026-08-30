<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Set once per request the first time activity() is called explicitly,
     * so the LogActivity middleware's generic catch-all can skip requests
     * that already got a specific, human-readable log entry.
     */
    protected static bool $loggedExplicitly = false;

    public static function log($action, $model = null, $modelId = null, $oldValues = null, $newValues = null, $userId = null)
    {
        return AuditLog::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    public static function activity($action, $description = null, $level = 'info', $userId = null)
    {
        static::$loggedExplicitly = true;

        return ActivityLog::create([
            'user_id' => $userId ?? auth()->id(),
            'level' => $level,
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    public static function hasLoggedExplicitly(): bool
    {
        return static::$loggedExplicitly;
    }
}
