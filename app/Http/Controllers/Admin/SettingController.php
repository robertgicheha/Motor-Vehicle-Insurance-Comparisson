<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChangeRequest;
use App\Models\Setting;
use App\Notifications\NewChangeRequest;
use App\Services\AuditService;
use Illuminate\Http\Request;
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
}
