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
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        $request->validate(['review_notes' => 'nullable|string|max:1000']);

        $this->applyChangeRequest($changeRequest);

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
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

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

    /**
     * Actually applies the change an admin proposed, once a super-admin
     * approves it. Mirrors exactly the field sets each admin-submitting
     * controller (CompanyController, SettingController) uses on its own
     * direct (super-admin) write path — not a redesign of those flows,
     * just performing on approval the write that was deferred at submission.
     */
    private function applyChangeRequest(ChangeRequest $changeRequest): void
    {
        $data = $changeRequest->data ?? [];

        switch ($changeRequest->type) {
            case 'setting':
                foreach ($data as $key => $value) {
                    $group = \App\Models\Setting::where('key', $key)->value('group') ?? 'general';
                    \App\Models\Setting::setValue($key, $value, $group);
                }
                break;

            case 'company':
                if ($changeRequest->action === 'create') {
                    \App\Models\Company::create($data);
                } elseif ($changeRequest->action === 'update') {
                    $company = \App\Models\Company::find($data['company_id'] ?? null);
                    if ($company) {
                        $company->update(collect($data)->except(['company_id', 'company_name'])->all());
                    } else {
                        AuditService::activity('change_request_apply_failed', "Change request #{$changeRequest->id} could not be applied: company no longer exists.", 'warning');
                    }
                } elseif (in_array($changeRequest->action, ['activate', 'deactivate'], true)) {
                    $company = \App\Models\Company::find($data['company_id'] ?? null);
                    if ($company) {
                        $company->update(['is_active' => $data['is_active']]);
                    } else {
                        AuditService::activity('change_request_apply_failed', "Change request #{$changeRequest->id} could not be applied: company no longer exists.", 'warning');
                    }
                }
                break;

            case 'rate':
                if ($changeRequest->action === 'create') {
                    $company = \App\Models\Company::find($data['company_id'] ?? null);
                    if ($company) {
                        $company->rates()->create(collect($data)->except(['company_id', 'company_name'])->all());
                    } else {
                        AuditService::activity('change_request_apply_failed', "Change request #{$changeRequest->id} could not be applied: company no longer exists.", 'warning');
                    }
                } elseif ($changeRequest->action === 'update') {
                    $rate = \App\Models\Rate::find($data['rate_id'] ?? null);
                    if ($rate) {
                        $rate->update(collect($data)->except(['rate_id', 'rate_name', 'company_id', 'company_name'])->all());
                    } else {
                        AuditService::activity('change_request_apply_failed', "Change request #{$changeRequest->id} could not be applied: rate no longer exists.", 'warning');
                    }
                } elseif ($changeRequest->action === 'delete') {
                    $rate = \App\Models\Rate::find($data['rate_id'] ?? null);
                    if ($rate) {
                        $rate->delete();
                    } else {
                        AuditService::activity('change_request_apply_failed', "Change request #{$changeRequest->id} could not be applied: rate already removed.", 'warning');
                    }
                }
                break;
        }
    }
}
