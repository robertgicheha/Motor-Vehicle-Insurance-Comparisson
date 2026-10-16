<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Sticker;
use App\Models\StickerEmailLog;
use App\Services\AuditService;
use Illuminate\Http\Request;

class StickerSettingsController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'email-config');

        $companies = Company::active()->get();

        $recentLogs = StickerEmailLog::with(['sticker', 'triggeredBy'])
            ->latest()
            ->paginate(25);

        $stats = [
            'total_sent' => StickerEmailLog::where('type', 'outbound_to_underwriter')->where('status', 'sent')->count(),
            'total_delivered' => StickerEmailLog::where('type', 'outbound_to_underwriter')->where('status', 'delivered')->count(),
            'total_failed' => StickerEmailLog::where('type', 'outbound_to_underwriter')->whereIn('status', ['failed', 'bounced'])->count(),
            'pending_responses' => Sticker::where('status', 'issued')->whereNull('response_received_at')->count(),
        ];

        $emailConfig = \App\Models\EmailSetting::whereIn('key', [
            'sticker_email_from_address', 'sticker_email_from_name', 'sticker_email_reply_to',
            'sticker_email_subject_template', 'sticker_email_cc', 'sticker_turnaround_minutes',
        ])->pluck('value', 'key');

        return view('admin.stickers.settings', compact('companies', 'recentLogs', 'stats', 'tab', 'emailConfig'));
    }

    public function emailSettings(Request $request)
    {
        $tab = $request->get('tab', 'email-config');

        $companies = Company::active()->get();

        $recentLogs = StickerEmailLog::with(['sticker', 'triggeredBy'])
            ->latest()
            ->paginate(25);

        $stats = [
            'total_sent' => StickerEmailLog::where('type', 'outbound_to_underwriter')->where('status', 'sent')->count(),
            'total_delivered' => StickerEmailLog::where('type', 'outbound_to_underwriter')->where('status', 'delivered')->count(),
            'total_failed' => StickerEmailLog::where('type', 'outbound_to_underwriter')->whereIn('status', ['failed', 'bounced'])->count(),
            'pending_responses' => Sticker::where('status', 'issued')->whereNull('response_received_at')->count(),
        ];

        $emailConfig = \App\Models\EmailSetting::whereIn('key', [
            'sticker_email_from_address', 'sticker_email_from_name', 'sticker_email_reply_to',
            'sticker_email_subject_template', 'sticker_email_cc', 'sticker_turnaround_minutes',
        ])->pluck('value', 'key');

        return view('admin.stickers.email-settings', compact('companies', 'recentLogs', 'stats', 'tab', 'emailConfig'));
    }

    public function updateEmailConfig(Request $request)
    {
        $validated = $request->validate([
            'from_address' => 'nullable|email|max:255',
            'from_name' => 'nullable|string|max:255',
            'reply_to' => 'nullable|email|max:255',
            'subject_template' => 'nullable|string|max:500',
            'cc_address' => 'nullable|email|max:255',
            'turnaround_minutes' => 'nullable|integer|min:5|max:1440',
        ]);

        $settings = [
            'sticker_email_from_address' => $validated['from_address'] ?? null,
            'sticker_email_from_name' => $validated['from_name'] ?? null,
            'sticker_email_reply_to' => $validated['reply_to'] ?? null,
            'sticker_email_subject_template' => $validated['subject_template'] ?? null,
            'sticker_email_cc' => $validated['cc_address'] ?? null,
            'sticker_turnaround_minutes' => $validated['turnaround_minutes'] ?? 30,
        ];

        foreach ($settings as $key => $value) {
            \App\Models\EmailSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'admin']
            );
        }

        return back()->with('success', 'Sticker email configuration updated successfully.');
    }

    public function logResponse(Request $request, Sticker $sticker)
    {
        $validated = $request->validate([
            'response_status' => 'required|in:pending,received,processing,completed',
            'response_notes' => 'nullable|string|max:2000',
            'response_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ]);

        $data = [
            'response_status' => $validated['response_status'],
            'response_notes' => $validated['response_notes'],
        ];

        if ($validated['response_status'] === 'completed') {
            $sticker->update([
                'status' => 'completed',
                'generated_at' => now(),
                'response_received_at' => now(),
            ]);
            $data['response_received_at'] = now();
        }

        if ($request->hasFile('response_document')) {
            $file = $request->file('response_document');
            $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '', $file->getClientOriginalName());
            $path = $file->storeAs('sticker-responses', $filename, 'public');
            $data['response_document_path'] = $path;
        }

        $sticker->update($data);

        StickerEmailLog::create([
            'sticker_id' => $sticker->id,
            'type' => 'inbound_from_underwriter',
            'subject' => 'Re: Sticker Issuance Request - ' . $sticker->sticker_number,
            'status' => 'received',
            'attachments' => $data['response_document_path'] ?? null,
            'metadata' => ['response_status' => $validated['response_status']],
        ]);

        AuditService::activity('sticker_response_logged', "Underwriter response logged for certificate {$sticker->sticker_number}: {$validated['response_status']}");

        return back()->with('success', 'Underwriter response logged successfully.');
    }
}
