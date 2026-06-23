<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
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

        foreach ($request->settings as $key => $value) {
            Setting::setValue($key, $value);
        }

        AuditService::activity('settings_updated', 'System settings updated by admin');

        return back()->with('success', 'Settings updated successfully.');
    }
}
