<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class EmailSettingController extends Controller
{
    public function index()
    {
        $settings = EmailSetting::all()->keyBy('key');
        return view('admin.email-settings.index', compact('settings'));
    }

    public function update(Request $request, EmailSetting $emailSetting)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'nullable|string|max:255',
            'body' => 'nullable|string',
            'letterhead_company' => 'nullable|string|max:255',
            'letterhead_color' => 'nullable|string|max:20',
            'letterhead_logo' => 'nullable|string|max:255',
            'letterhead_address' => 'nullable|string',
            'letterhead_website' => 'nullable|string|max:255',
            'letterhead_logo_header' => 'sometimes|boolean',
            'letterhead_logo_footer' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $data['letterhead_logo_header'] = $request->boolean('letterhead_logo_header');
        $data['letterhead_logo_footer'] = $request->boolean('letterhead_logo_footer');

        $emailSetting->update($data);

        return back()->with('success', 'Email setting updated successfully.');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:email_settings,key',
            'subject' => 'nullable|string|max:255',
            'body' => 'nullable|string',
            'type' => 'required|in:user,admin,superadmin',
            'letterhead_company' => 'nullable|string|max:255',
            'letterhead_color' => 'nullable|string|max:20',
            'letterhead_address' => 'nullable|string',
            'letterhead_website' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        EmailSetting::create($validator->validated());

        return back()->with('success', 'Email template created successfully.');
    }

    public function destroy(EmailSetting $emailSetting)
    {
        $emailSetting->delete();

        return back()->with('success', 'Email template deleted successfully.');
    }
}
