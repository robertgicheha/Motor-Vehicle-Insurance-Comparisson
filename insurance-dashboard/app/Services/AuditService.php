<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public static function log($action, $model = null, $modelId = null, $oldValues = null, $newValues = null)
    {
        return AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    public static function activity($action, $description = null, $level = 'info')
    {
        return ActivityLog::create([
            'user_id' => auth()->id(),
            'level' => $level,
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
