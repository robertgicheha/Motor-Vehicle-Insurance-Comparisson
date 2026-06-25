<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChangeRequest;
use App\Models\User;
use App\Notifications\ChangeRequestNotification;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ChangeRequestController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super-admin');

        if ($isSuperAdmin) {
            $requests = ChangeRequest::with(['user', 'reviewer'])
                ->latest()
                ->paginate(20);
            $pendingCount = ChangeRequest::pending()->count();
        } else {
            $requests = ChangeRequest::with(['user', 'reviewer'])
                ->forUser($user->id)
                ->latest()
                ->paginate(20);
            $pendingCount = ChangeRequest::forUser($user->id)->pending()->count();
        }

        return view('admin.change-requests.index', compact('requests', 'pendingCount', 'isSuperAdmin'));
    }

    public function show(ChangeRequest $changeRequest)
    {
        $changeRequest->load(['user', 'reviewer']);
        return view('admin.change-requests.show', compact('changeRequest'));
    }

    public function approve(ChangeRequest $changeRequest, Request $request)
    {
        $request->validate(['review_notes' => 'nullable|string|max:1000']);

        $changeRequest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'review_notes' => $request->review_notes,
            'reviewed_at' => now(),
        ]);

        $changeRequest->user->notify(new ChangeRequestNotification(
            $changeRequest,
            'approved',
            $request->review_notes
        ));

        AuditService::activity('change_request_approved', "Change request #{$changeRequest->id} approved by " . auth()->user()->name);

        return redirect()->route('admin.change-requests.index')
            ->with('success', 'Change request approved.');
    }

    public function decline(ChangeRequest $changeRequest, Request $request)
    {
        $request->validate(['review_notes' => 'nullable|string|max:1000']);

        $changeRequest->update([
            'status' => 'declined',
            'reviewed_by' => auth()->id(),
            'review_notes' => $request->review_notes,
            'reviewed_at' => now(),
        ]);

        $changeRequest->user->notify(new ChangeRequestNotification(
            $changeRequest,
            'declined',
            $request->review_notes
        ));

        AuditService::activity('change_request_declined', "Change request #{$changeRequest->id} declined by " . auth()->user()->name);

        return redirect()->route('admin.change-requests.index')
            ->with('success', 'Change request declined.');
    }
}
