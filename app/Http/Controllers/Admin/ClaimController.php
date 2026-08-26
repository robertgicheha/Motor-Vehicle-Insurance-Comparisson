<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
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
            $query->where('registration_number', 'like', '%' . $request->registration_number . '%');
        }

        if ($request->filled('claim_number')) {
            $query->where('claim_number', 'like', '%' . $request->claim_number . '%');
        }

        $claims = $query->paginate(20);

        $stats = [
            'total' => Claim::count(),
            'reported' => Claim::where('status', Claim::STATUS_REPORTED)->count(),
            'documents_pending' => Claim::where('status', Claim::STATUS_DOCUMENTS_PENDING)->count(),
            'under_review' => Claim::where('status', Claim::STATUS_UNDER_REVIEW)->count(),
            'settlement' => Claim::where('status', Claim::STATUS_SETTLEMENT)->count(),
            'under_investigation' => Claim::where('status', Claim::STATUS_UNDER_INVESTIGATION)->count(),
            'settled' => Claim::where('status', Claim::STATUS_SETTLED)->count(),
            'declined' => Claim::where('status', Claim::STATUS_DECLINED)->count(),
        ];

        return view('admin.claims.index', compact('claims', 'stats'));
    }

    public function show(Claim $claim)
    {
        $claim->load(['user', 'onBehalfOfUser']);

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

        if ($newStatus === Claim::STATUS_UNDER_REVIEW) {
            $updateData['reviewed_at'] = now();
        }

        if ($newStatus === Claim::STATUS_SETTLED) {
            $updateData['settled_at'] = now();
            $updateData['settlement_amount'] = $request->settlement_amount;
            $updateData['settlement_notes'] = $request->settlement_notes;
        }

        if ($newStatus === Claim::STATUS_DECLINED) {
            $updateData['declined_at'] = now();
        }

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
}
