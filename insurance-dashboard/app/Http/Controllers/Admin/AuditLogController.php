<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function indexAudit(Request $request)
    {
        $query = AuditLog::with('user');
        if ($request->action) {
            $query->where('action', 'like', "%{$request->action}%");
        }
        if ($request->model) {
            $query->where('model', $request->model);
        }
        $logs = $query->latest()->paginate(30);
        return view('admin.audit.index', compact('logs'));
    }

    public function indexActivity(Request $request)
    {
        $query = ActivityLog::with('user');
        if ($request->action) {
            $query->where('action', 'like', "%{$request->action}%");
        }
        if ($request->level) {
            $query->where('level', $request->level);
        }
        $logs = $query->latest()->paginate(30);
        return view('admin.audit.activity', compact('logs'));
    }
}
