<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ActivityLog;
use App\Models\User;
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

        $infoCount = 0;
        $warningCount = 0;
        $errorCount = 0;

        return view('admin.audit.index', compact('logs', 'infoCount', 'warningCount', 'errorCount'));
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

        $infoCount = ActivityLog::where('level', 'info')->count();
        $warningCount = ActivityLog::where('level', 'warning')->count();
        $errorCount = ActivityLog::where('level', 'error')->count();

        return view('admin.audit.activity', compact('logs', 'infoCount', 'warningCount', 'errorCount'));
    }

    /**
     * A single user's full story — a chronological, de-duplicated timeline of
     * every action they took. Sourced from the human-readable activity log
     * only (not merged with the audit trail), so each event appears exactly
     * once. The structured before/after changes live on the Audit Trail view.
     */
    public function userStory(Request $request)
    {
        $users = User::orderBy('username')->get(['id', 'username']);
        $selectedUser = null;
        $timeline = null;
        $perPage = 25;

        if ($request->filled('user_id')) {
            $selectedUser = User::find($request->user_id);
        }

        if ($selectedUser) {
            $timeline = ActivityLog::where('user_id', $selectedUser->id)
                ->latest()
                ->paginate($perPage)
                ->through(function ($log) {
                    return (object) [
                        'type' => 'activity',
                        'action' => $log->action,
                        'description' => $log->description,
                        'model' => null,
                        'model_id' => null,
                        'old_values' => null,
                        'new_values' => null,
                        'level' => $log->level,
                        'ip_address' => $log->ip_address,
                        'user_agent' => $log->user_agent,
                        'created_at' => $log->created_at,
                    ];
                });
        }

        return view('admin.audit.story', compact('users', 'selectedUser', 'timeline'));
    }
}
