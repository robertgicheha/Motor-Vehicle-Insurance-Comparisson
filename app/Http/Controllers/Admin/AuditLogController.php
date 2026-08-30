<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

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
        if ($request->level) {
            $query->where('level', $request->level);
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
     * A single user's full story — every audit + activity entry for that
     * user, merged into one chronological timeline.
     */
    public function userStory(Request $request)
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $selectedUser = null;
        $timeline = null;
        $perPage = 25;

        if ($request->filled('user_id')) {
            $selectedUser = User::find($request->user_id);
        }

        if ($selectedUser) {
            $auditEntries = AuditLog::where('user_id', $selectedUser->id)->get()->map(function ($log) {
                return (object) [
                    'type' => 'audit',
                    'action' => $log->action,
                    'description' => trim(($log->action ? ucfirst(str_replace('_', ' ', $log->action)) : 'Action') . ($log->model ? " on {$log->model}" . ($log->model_id ? " #{$log->model_id}" : '') : '')),
                    'model' => $log->model,
                    'model_id' => $log->model_id,
                    'old_values' => $log->old_values,
                    'new_values' => $log->new_values,
                    'level' => null,
                    'ip_address' => $log->ip_address,
                    'user_agent' => $log->user_agent,
                    'created_at' => $log->created_at,
                ];
            });

            $activityEntries = ActivityLog::where('user_id', $selectedUser->id)->get()->map(function ($log) {
                return (object) [
                    'type' => 'activity',
                    'action' => $log->action,
                    'description' => $log->description ?? $log->action,
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

            $allEntries = $auditEntries->concat($activityEntries)->sortByDesc('created_at')->values();

            $page = (int) $request->get('page', 1);
            $slice = $allEntries->forPage($page, $perPage)->values();

            $timeline = new LengthAwarePaginator(
                $slice,
                $allEntries->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        return view('admin.audit.story', compact('users', 'selectedUser', 'timeline'));
    }
}
