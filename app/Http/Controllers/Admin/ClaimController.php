<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\ClaimEmailLog;
use App\Models\User;
use App\Notifications\ClaimStatusChanged;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    public function index(Request $request)
    {
        $query = Claim::with(['user', 'onBehalfOfUser'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('registration_number')) {
            $query->whereLikeCI('registration_number', $request->registration_number);
        }

        if ($request->filled('claim_number')) {
            $query->where('claim_number', 'like', '%' . $request->claim_number . '%');
        }

        $claims = $query->paginate(20);

        $stats = [
            'total' => Claim::count(),
            'reported' => Claim::where('status', Claim::STATUS_REPORTED)->count(),
        ];

        return view('admin.claims.index', compact('claims', 'stats'));
    }

    public function show(Claim $claim)
    {
        $claim->load(['user', 'onBehalfOfUser', 'documents']);

        return view('admin.claims.show', compact('claim'));
    }

    public function updateStatus(Request $request, Claim $claim)
    {
        $request->validate([
            'status' => 'required|string|in:' . implode(',', array_keys(Claim::statuses())),
            'admin_notes' => 'nullable|string|max:2000',
            'settlement_amount' => 'nullable|numeric|min:0',
            'settlement_notes' => 'nullable|string|max:2000',
        ]);

        $oldStatus = $claim->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return back()->with('info', 'Claim status is already ' . Claim::statuses()[$newStatus] . '.');
        }

        $updateData = [
            'status' => $newStatus,
            'admin_notes' => $request->admin_notes,
        ];

        $claim->update($updateData);

        if ($claim->user) {
            $claim->user->notify(new ClaimStatusChanged($claim, $oldStatus, $newStatus));
        }
        if ($claim->onBehalfOfUser) {
            $claim->onBehalfOfUser->notify(new ClaimStatusChanged($claim, $oldStatus, $newStatus));
        }

        $admins = User::role(['super-admin', 'admin'])->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new ClaimStatusChanged($claim, $oldStatus, $newStatus));
        }

        return back()->with('success', 'Claim status updated to ' . Claim::statuses()[$newStatus] . '.');
    }

    public function emailLogs(Request $request)
    {
        $query = ClaimEmailLog::with(['claim', 'triggeredBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->latest()->paginate(25)->withQueryString();

        $stats = [
            'total' => ClaimEmailLog::count(),
            'sent' => ClaimEmailLog::where('status', 'sent')->count(),
            'failed' => ClaimEmailLog::where('status', 'failed')->count(),
        ];

        return view('admin.claims.email-logs', compact('logs', 'stats'));
    }
}
