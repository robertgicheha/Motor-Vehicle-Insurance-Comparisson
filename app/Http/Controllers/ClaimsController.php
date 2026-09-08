<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\ClaimDocument;
use App\Models\Company;
use App\Models\Quotation;
use App\Models\User;
use App\Notifications\ClaimStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ClaimsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('super-admin') || $user->hasRole('admin');

        $query = Claim::with(['user', 'onBehalfOfUser'])
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('registration_number')) {
            $query->whereLikeCI('registration_number', $request->registration_number);
        }

        $claims = $query->paginate(20);

        return view('claims.index', compact('claims', 'isAdmin'));
    }

    public function myClaims(Request $request)
    {
        $user = auth()->user();

        $claims = Claim::with(['user', 'onBehalfOfUser'])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('on_behalf_of_user_id', $user->id);
            })
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('claims.index', compact('claims'));
    }

    public function create()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('super-admin') || $user->hasRole('admin');

        $users = User::whereDoesntHave('roles', function ($q) {
            $q->whereIn('name', ['super-admin', 'admin']);
        })->orderBy('name')->get(['id', 'name', 'email', 'first_name', 'last_name', 'mobile']);

        $admins = User::role(['super-admin', 'admin'])->orderBy('name')->get(['id', 'name', 'email', 'first_name', 'last_name', 'mobile']);

        $self = [
            'name' => trim($user->first_name . ' ' . $user->last_name) ?: $user->name,
            'email' => $user->email,
            'phone' => $user->mobile ?? '',
        ];

        $companies = Company::active()->get(['id', 'name', 'slug', 'logo']);

        $companiesForJs = $companies->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'slug' => $c->slug,
            'logo' => $c->logoUrl(),
        ])->values();

        // Reuse the same token across a failed-validation re-render (old()
        // carries it back) so attachments uploaded before the failed submit
        // stay linked to this attempt instead of being orphaned under a
        // token the page will never reference again.
        $uploadToken = old('upload_token') ?: (string) \Illuminate\Support\Str::uuid();

        $stagedDocuments = ClaimDocument::where('upload_token', $uploadToken)
            ->whereNull('claim_id')
            ->get()
            ->map(fn($d) => [
                'id' => $d->id,
                'document_type' => $d->document_type,
                'filename' => $d->filename,
            ]);

        return view('claims.create', compact('users', 'admins', 'isAdmin', 'self', 'companies', 'companiesForJs', 'uploadToken', 'stagedDocuments'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'registration_number' => 'required|string|max:50',
            'vehicle_make' => 'nullable|string|max:255',
            'vehicle_model' => 'nullable|string|max:255',
            'vehicle_year' => 'nullable|integer|min:1990|max:2030',
            'vehicle_color' => 'nullable|string|max:50',
            'engine_number' => 'nullable|string|max:100',
            'chassis_number' => 'nullable|string|max:100',
            'cover_type' => 'nullable|in:private,psv',
            'vehicle_valuation' => 'nullable|numeric|min:0',
            'insurance_company_id' => 'nullable|exists:companies,id',
            'incident_date' => 'required|date',
            'incident_description' => 'required|string|max:2000',
            'police_notified' => 'nullable|boolean',
            'claim_type' => 'required|in:' . implode(',', array_keys(Claim::claimTypes())),
            'claim_type_other' => 'required_if:claim_type,others|nullable|string|max:1000',
            'upload_token' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = auth()->user();
        $isAdmin = $user->hasRole('super-admin') || $user->hasRole('admin');

        // Customer identity is resolved server-side from the matched
        // quotation, never trusted from the client — a regular user could
        // otherwise submit an arbitrary name/email as the "claimant". An
        // admin filing on a customer's behalf is what on_behalf_of_user_id
        // is for; a plain user only ever matches their own quotations (see
        // the same scoping in lookupVehicle()/suggestVehicles() below).
        $registration = strtoupper(trim($request->registration_number));
        $quotation = Quotation::with('user')
            ->whereRaw('UPPER(car_registration) = ?', [$registration])
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->latest()
            ->first();

        $onBehalfOfUserId = null;
        if ($quotation) {
            // Always the quotation's own captured customer fields — not the
            // account it's stored under. An admin/superadmin can generate a
            // quotation on a customer's behalf, in which case
            // $quotation->user is the staff member, not the claimant; the
            // real customer's salutation/name/email live on the quotation
            // itself (customer_first_name / customer_last_name / etc.).
            $customerName = $quotation->customer_name;
            $customerEmail = $quotation->customer_email;
            $customerPhone = $quotation->customer_phone;
            if ($quotation->user && $quotation->user->id !== $user->id) {
                $onBehalfOfUserId = $quotation->user->id;
            }
        } else {
            // No matching quotation — the person filing is the claimant.
            $customerName = trim($user->first_name . ' ' . $user->last_name) ?: $user->name;
            $customerEmail = $user->email;
            $customerPhone = $user->mobile ?? '';
        }

        $claim = Claim::create([
            'claim_number' => Claim::generateNumber(),
            'user_id' => $user->id,
            'on_behalf_of_user_id' => $onBehalfOfUserId,
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'customer_phone' => $customerPhone,
            'registration_number' => $request->registration_number,
            'vehicle_details' => [
                'make' => $request->vehicle_make,
                'model' => $request->vehicle_model,
                'year' => $request->vehicle_year,
                'color' => $request->vehicle_color,
                'engine_number' => $request->engine_number,
                'chassis_number' => $request->chassis_number,
                'cover_type' => $request->cover_type,
                'valuation' => $request->vehicle_valuation,
                'insurance_company_id' => $request->insurance_company_id,
            ],
            'incident_date' => $request->incident_date,
            'incident_description' => $request->incident_description,
            'claim_type' => $request->claim_type,
            'claim_type_other' => $request->claim_type === 'others' ? $request->claim_type_other : null,
            'police_notified' => $request->boolean('police_notified'),
            'status' => Claim::STATUS_REPORTED,
        ]);

        // Attach whatever the issuer uploaded (police abstract / incident
        // photos) before the claim existed — staged under upload_token.
        if ($request->filled('upload_token')) {
            ClaimDocument::where('upload_token', $request->upload_token)
                ->whereNull('claim_id')
                ->update(['claim_id' => $claim->id]);
        }

        $admins = User::role(['super-admin', 'admin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new ClaimStatusChanged($claim, 'new', Claim::STATUS_REPORTED));
        }

        if ($claim->onBehalfOfUser) {
            $claim->onBehalfOfUser->notify(new ClaimStatusChanged($claim, 'new', Claim::STATUS_REPORTED));
        }

        // Underwriter recipient is resolved server-side from the company on
        // the claim (claims_email is the dedicated claims-desk address;
        // underwriter_email is the fallback), not a value the client could
        // tamper with.
        $claimCompany = $request->filled('insurance_company_id')
            ? Company::find($request->insurance_company_id)
            : null;
        $toAddress = $claimCompany?->claims_email ?: $claimCompany?->underwriter_email;

        if ($toAddress) {
            $fromAddress = $this->claimEmailFromAddress();
            $ccAddress = $this->claimEmailCc($claimCompany);
            $emailStatus = 'sent';
            $emailError = null;

            try {
                $claimEmailData = [
                    'claim_number' => $claim->claim_number,
                    'customer_name' => $claim->customer_name,
                    'customer_email' => $claim->customer_email,
                    'customer_phone' => $claim->customer_phone,
                    'registration_number' => $claim->registration_number,
                    'vehicle_details' => $claim->vehicle_details,
                    'incident_date' => $claim->incident_date,
                    'incident_description' => $claim->incident_description,
                    'police_notified' => $claim->police_notified,
                    'claim_type' => Claim::claimTypes()[$claim->claim_type] ?? $claim->claim_type,
                    'claim_type_other' => $claim->claim_type_other,
                    'company_name' => $claimCompany?->name ?? 'N/A',
                ];

                $attachmentPaths = $claim->documents()
                    ->get()
                    ->map(fn($d) => storage_path('app/public/' . $d->path))
                    ->filter(fn($p) => file_exists($p))
                    ->values()
                    ->all();

                $mailable = (new \App\Mail\ClaimLoggedMail($claimEmailData, $attachmentPaths))
                    ->from($fromAddress, $this->claimEmailFromName());

                Mail::to($toAddress)->cc($ccAddress)->send($mailable);
                \App\Services\AuditService::activity('claim_emailed', "Claim {$claim->claim_number} emailed to underwriter {$toAddress}");
            } catch (\Exception $e) {
                $emailStatus = 'failed';
                $emailError = $e->getMessage();
                \Log::error("Failed to send claim email to underwriter: " . $e->getMessage());
                \App\Services\AuditService::activity('claim_email_failed', "Failed to email claim {$claim->claim_number} to {$toAddress}: {$e->getMessage()}", 'error');
            }

            \App\Models\ClaimEmailLog::create([
                'claim_id' => $claim->id,
                'type' => 'outbound_to_underwriter',
                'recipient' => $toAddress,
                'cc' => $ccAddress,
                'sender' => $fromAddress,
                'subject' => 'New Claim Logged - ' . $claim->claim_number,
                'status' => $emailStatus,
                'error_message' => $emailError,
                'triggered_by' => $user->id,
                'sent_at' => $emailStatus === 'sent' ? now() : null,
            ]);
        } elseif ($claimCompany) {
            \Log::warning("Claim {$claim->claim_number}: company {$claimCompany->name} has no claims_email or underwriter_email configured — no underwriter notification sent.");
        }

        $redirectRoute = $isAdmin ? route('admin.claims.show', $claim) : route('claims.show', $claim);

        return redirect($redirectRoute)
            ->with('success', 'Claim logged successfully! Claim number: ' . $claim->claim_number);
    }

    public function lookupVehicle(Request $request)
    {
        $request->validate([
            'registration_number' => 'required|string|max:50',
        ]);

        $user = auth()->user();
        $isAdmin = $user->hasRole('super-admin') || $user->hasRole('admin');
        $registration = strtoupper(trim($request->registration_number));

        $quotation = Quotation::with(['user', 'company'])
            ->whereRaw('UPPER(car_registration) = ?', [$registration])
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->latest()
            ->first();

        if (!$quotation) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'registration_number' => $quotation->car_registration,
            'customer_name' => $quotation->customer_name,
            'customer_email' => $quotation->customer_email ?? '',
            'customer_phone' => $quotation->customer_phone ?? '',
            'vehicle' => [
                'make' => $quotation->car_make,
                'model' => $quotation->car_model,
                'year' => $quotation->car_year,
                'color' => $quotation->color,
                'engine_number' => $quotation->engine_number,
                'chassis_number' => $quotation->chassis_number,
                'valuation' => $quotation->car_valuation,
                'cover_type' => $quotation->vehicle_type,
            ],
            'company_name' => $quotation->company?->name ?? '',
            'company_id' => $quotation->company_id,
        ]);
    }

    public function suggestVehicles(Request $request)
    {
        $q = strtoupper(trim($request->get('q', '')));
        if (strlen($q) < 3) {
            return response()->json(['suggestions' => []]);
        }

        $user = auth()->user();
        $isAdmin = $user->hasRole('super-admin') || $user->hasRole('admin');
        $driver = DB::connection()->getDriverName();

        // Substring match via each engine's own regex support (MySQL REGEXP,
        // SQLite REGEXP requires a UDF Laravel doesn't register, so it falls
        // back to LIKE there) — the input is regex-escaped so this always
        // behaves as a literal contains-match, never an arbitrary pattern
        // the caller could turn into a slow/catastrophic regex.
        $pattern = preg_quote($q, '/');

        $query = Quotation::select('id', 'car_registration', 'car_make', 'car_model', 'car_year', 'vehicle_type', 'created_at')
            ->when(!$isAdmin, fn($qq) => $qq->where('user_id', $user->id));

        if ($driver === 'mysql') {
            $query->whereRaw('UPPER(car_registration) REGEXP ?', [$pattern]);
        } else {
            $query->whereRaw('UPPER(car_registration) LIKE ?', ['%' . $q . '%']);
        }

        // GROUP BY car_registration while selecting id/make/model/etc would
        // fail under MySQL's only_full_group_by (the same class of bug fixed
        // elsewhere in this app) — dedupe in PHP instead, keeping the most
        // recent quotation per registration.
        $suggestions = $query->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->unique('car_registration')
            ->take(10)
            ->map(fn($row) => [
                'registration' => $row->car_registration,
                'make' => $row->car_make,
                'model' => $row->car_model,
                'year' => $row->car_year,
                'type' => $row->vehicle_type,
            ])
            ->values();

        return response()->json(['suggestions' => $suggestions]);
    }

    /**
     * Uploads one attachment for a claim that doesn't exist yet — staged
     * under upload_token and linked to the real Claim row once store()
     * creates it. police_abstract is a single optional slot (a re-upload
     * replaces it); incident_photo is open-ended, one independent row per
     * photo, matching the same pattern used for the sticker's alternate KYC
     * documents.
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'upload_token' => 'required|string|max:100',
            'document_type' => 'required|in:police_abstract,incident_photo',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('document');
        $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '', $file->getClientOriginalName());
        $path = $file->storeAs('claim-documents', $filename, 'public');

        $existingDoc = $request->document_type === 'police_abstract'
            ? ClaimDocument::where('upload_token', $request->upload_token)
                ->whereNull('claim_id')
                ->where('document_type', 'police_abstract')
                ->first()
            : null;

        if ($existingDoc) {
            Storage::disk('public')->delete($existingDoc->path);
            $existingDoc->update([
                'filename' => $filename,
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
            $documentId = $existingDoc->id;
        } else {
            $doc = ClaimDocument::create([
                'upload_token' => $request->upload_token,
                'document_type' => $request->document_type,
                'filename' => $filename,
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'scan_status' => 'clean',
                'scan_result' => 'Document accepted.',
                'uploaded_by' => auth()->id(),
            ]);
            $documentId = $doc->id;
        }

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully.',
            'document_type' => $request->document_type,
            'filename' => $filename,
            'document_id' => $documentId,
        ]);
    }

    public function removeDocument(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:claim_documents,id',
        ]);

        $doc = ClaimDocument::findOrFail($request->document_id);

        // Only removable before it's attached to a real claim (staged, no
        // claim_id yet) and only by whoever uploaded it — once a claim is
        // submitted its attachments are part of the record.
        if ($doc->claim_id !== null || $doc->uploaded_by !== auth()->id()) {
            return response()->json(['error' => 'This document can no longer be removed.'], 403);
        }

        Storage::disk('public')->delete($doc->path);
        $doc->delete();

        return response()->json(['success' => true]);
    }

    public function show(Claim $claim)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('super-admin') || $user->hasRole('admin');

        if (!$isAdmin && $claim->user_id !== $user->id && $claim->on_behalf_of_user_id !== $user->id) {
            abort(403);
        }

        $claim->load(['user', 'onBehalfOfUser', 'documents']);

        return view('claims.show', compact('claim', 'isAdmin'));
    }

    public function updateStatus(Request $request, Claim $claim)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('super-admin') || $user->hasRole('admin');

        if (!$isAdmin) {
            abort(403);
        }

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

    public function updateDocumentsConfirmed(Request $request, Claim $claim)
    {
        $user = auth()->user();

        if ($claim->user_id !== $user->id && $claim->on_behalf_of_user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'documents_confirmed' => 'nullable|array',
            'documents_confirmed.*' => 'in:claim_form,police_abstract,drivers_licence,log_book,drivers_statement,other',
        ]);

        $claim->update([
            'documents_confirmed' => $request->documents_confirmed ?? [],
        ]);

        return back()->with('success', 'Document confirmation updated.');
    }

    public function updateVehicleAssessment(Request $request, Claim $claim)
    {
        $user = auth()->user();

        if ($claim->user_id !== $user->id && $claim->on_behalf_of_user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'vehicle_assessment_done' => 'required|boolean',
        ]);

        $claim->update([
            'vehicle_assessment_done' => $request->boolean('vehicle_assessment_done'),
        ]);

        return back()->with('success', 'Vehicle assessment status updated.');
    }

    public function publicTracker(Request $request)
    {
        $registrationNumber = strtoupper(trim($request->get('registration_number', '')));

        $claims = collect();
        $searched = false;

        if ($registrationNumber) {
            $searched = true;
            $claims = Claim::with(['user', 'onBehalfOfUser'])
                ->whereEqualsCI('registration_number', $registrationNumber)
                ->latest()
                ->get();
        }

        return view('claims.public-tracker', compact('claims', 'registrationNumber', 'searched'));
    }

    /**
     * The noreply sender address for underwriter emails — shares the same
     * setting configured under Admin > Stickers > Settings > Email Configuration.
     */
    protected function claimEmailFromAddress(): string
    {
        return \App\Models\EmailSetting::where('key', 'sticker_email_from_address')->value('value')
            ?: config('mail.from.address');
    }

    protected function claimEmailFromName(): string
    {
        return \App\Models\EmailSetting::where('key', 'sticker_email_from_name')->value('value')
            ?: config('mail.from.name');
    }

    /**
     * CC address for underwriter emails: the company's own CC-claims address
     * takes priority, then the global Sticker Settings CC, then the app default.
     */
    protected function claimEmailCc(?Company $company): ?string
    {
        return $company?->cc_claims_email
            ?: \App\Models\EmailSetting::where('key', 'sticker_email_cc')->value('value')
            ?: config('mail.from.address');
    }
}
