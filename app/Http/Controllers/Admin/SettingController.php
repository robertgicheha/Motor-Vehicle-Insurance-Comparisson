<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChangeRequest;
use App\Models\Setting;
use App\Notifications\NewChangeRequest;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*' => 'nullable|string',
        ]);

        if ($validated->fails()) {
            return back()->withErrors($validated);
        }

        if (auth()->user()->hasRole('admin')) {
            $changeRequest = ChangeRequest::create([
                'user_id' => auth()->id(),
                'type' => 'setting',
                'action' => 'update',
                'data' => $request->settings,
                'reason' => $request->input('reason', ''),
            ]);
            $superAdmins = \App\Models\User::role('super-admin')->get();
            foreach ($superAdmins as $sa) {
                $sa->notify(new NewChangeRequest($changeRequest));
            }
            AuditService::activity('settings_update_requested', 'Settings update requested by ' . auth()->user()->name);
            return back()->with('info', 'Your settings update request has been sent to the super-admin for approval.');
        }

        foreach ($request->settings as $key => $value) {
            Setting::setValue($key, $value);
        }

        AuditService::activity('settings_updated', 'System settings updated by admin');

        return back()->with('success', 'Settings updated successfully.');
    }

    private function runCacheCommand(string $command, string $label): \Illuminate\Http\RedirectResponse
    {
        try {
            Artisan::call($command);
            AuditService::activity('cache_' . str_replace('.', '_', $command), $label . ' executed by ' . auth()->user()->name);
            return back()->with('success', $label . ' completed successfully.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Cache command {$command} failed: " . $e->getMessage());
            return back()->with('error', 'Failed to run ' . $label . '. Please check the logs.');
        }
    }

    public function clearCache()
    {
        return $this->runCacheCommand('cache:clear', 'Application cache cleared');
    }

    public function clearConfigCache()
    {
        return $this->runCacheCommand('config:clear', 'Config cache cleared');
    }

    public function clearViewCache()
    {
        return $this->runCacheCommand('view:clear', 'View cache cleared');
    }

    public function clearRouteCache()
    {
        return $this->runCacheCommand('route:clear', 'Route cache cleared');
    }

    public function optimize()
    {
        return $this->runCacheCommand('optimize', 'System optimized');
    }

    public function clearAllCaches()
    {
        try {
            Artisan::call('optimize:clear');
            AuditService::activity('cache_all_cleared', 'All caches cleared by ' . auth()->user()->name);
            return back()->with('success', 'All caches cleared successfully.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Cache clear-all failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to clear all caches. Please check the logs.');
        }
    }
}
