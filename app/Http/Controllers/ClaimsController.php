<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Company;
use App\Models\Quotation;
use App\Models\User;
use App\Notifications\ClaimStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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
            'claims_email' => $c->claims_email,
        ])->values();

        return view('claims.create', compact('users', 'admins', 'isAdmin', 'self', 'companies','companiesForJs'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'claim_for' => 'required|in:self,user,admin,customer',
            'customer_name' => 'required_if:claim_for,customer|string|max:255',
            'customer_email' => 'required_if:claim_for,customer|nullable|email|max:255',
            'customer_phone' => 'required_if:claim_for,customer|nullable|string|max:50',
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
            'on_behalf_of' => 'nullable|exists:users,id',
            'documents_confirmed' => 'nullable|array',
            'documents_confirmed.*' => 'in:claim_form,police_abstract,drivers_licence,log_book,drivers_statement,other',
            'other_documents' => 'nullable|array',
            'other_documents.*' => 'nullable|string|max:500',
            'selected_underwriter_email' => 'nullable|email',
            'selected_underwriter_id' => 'nullable|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $isAdmin = auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin');
        $claimFor = $request->claim_for;
        $targetUserId = auth()->id();

        if ($isAdmin && ($claimFor === 'user' || $claimFor === 'admin') && $request->filled('on_behalf_of')) {
            $targetUserId = (int) $request->on_behalf_of;
        }

        [$customerName, $customerEmail, $customerPhone] = $this->resolveCustomerDetails($request, $claimFor, $targetUserId);

        $claim = Claim::create([
            'claim_number' => Claim::generateNumber(),
            'user_id' => auth()->id(),
            'on_behalf_of_user_id' => in_array($claimFor, ['user', 'admin']) && $targetUserId !== auth()->id() ? $targetUserId : null,
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
                'insurance_company' => $request->insurance_company,
                'insurance_company_id' => $request->insurance_company_id,
            ],
            'incident_date' => $request->incident_date,
            'incident_description' => $request->incident_description,
            'police_notified' => $request->boolean('police_notified'),
            'documents_confirmed' => $request->documents_confirmed ?? [],
            'other_documents' => array_values(array_filter($request->other_documents ?? [])),
            'status' => Claim::STATUS_REPORTED,
        ]);

        $admins = User::role(['super-admin', 'admin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new ClaimStatusChanged($claim, 'new', Claim::STATUS_REPORTED));
        }

        if ($claim->onBehalfOfUser) {
            $claim->onBehalfOfUser->notify(new ClaimStatusChanged($claim, 'new', Claim::STATUS_REPORTED));
        }

        if ($request->filled('selected_underwriter_email')) {
            $claimCompany = $request->filled('selected_underwriter_id')
                ? Company::find($request->selected_underwriter_id)
                : null;

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
                    'company_name' => $claimCompany?->name ?? 'N/A',
                ];

                $mailable = (new \App\Mail\ClaimLoggedMail($claimEmailData))->from($fromAddress, $this->claimEmailFromName());
                $toAddress = $request->selected_underwriter_email;

                Mail::to($toAddress)->cc($ccAddress)->send($mailable);
                \App\Services\AuditService::activity('claim_emailed', "Claim {$claim->claim_number} emailed to underwriter {$request->selected_underwriter_email}");
            } catch (\Exception $e) {
                $emailStatus = 'failed';
                $emailError = $e->getMessage();
                \Log::error("Failed to send claim email to underwriter: " . $e->getMessage());
                \App\Services\AuditService::activity('claim_email_failed', "Failed to email claim {$claim->claim_number} to {$request->selected_underwriter_email}: {$e->getMessage()}", 'error');
            }

            \App\Models\ClaimEmailLog::create([
                'claim_id' => $claim->id,
                'type' => 'outbound_to_underwriter',
                'recipient' => $request->selected_underwriter_email,
                'cc' => $ccAddress,
                'sender' => $fromAddress,
                'subject' => 'New Claim Logged - ' . $claim->claim_number,
                'status' => $emailStatus,
                'error_message' => $emailError,
                'triggered_by' => auth()->id(),
                'sent_at' => $emailStatus === 'sent' ? now() : null,
            ]);
        }

        return redirect()->route('claims.index')
            ->with('success', 'Claim logged successfully! Claim number: ' . $claim->claim_number);
    }

    protected function resolveCustomerDetails(Request $request, string $claimFor, int $targetUserId): array
    {
        if ($claimFor === 'customer') {
            return [$request->customer_name, $request->customer_email, $request->customer_phone];
        }

        $target = $targetUserId === auth()->id()
            ? auth()->user()
            : User::find($targetUserId);

        if ($target) {
            $name = trim($target->first_name . ' ' . $target->last_name) ?: $target->name;
            return [$name, $target->email, $target->mobile ?? ''];
        }

        return [$request->customer_name ?? '', $request->customer_email ?? '', $request->customer_phone ?? ''];
    }

    public function lookupVehicle(Request $request)
    {
        $request->validate([
            'registration_number' => 'required|string|max:50',
        ]);

        $registration = strtoupper(trim($request->registration_number));

        $quotation = Quotation::with(['user', 'company'])
            ->whereRaw('UPPER(car_registration) = ?', [$registration])
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
        if (strlen($q) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $suggestions = Quotation::whereRaw('UPPER(car_registration) LIKE ?', ['%' . $q . '%'])
            ->select('id', 'car_registration', 'car_make', 'car_model', 'car_year', 'vehicle_type')
            ->groupBy('car_registration')
            ->limit(10)
            ->get()
            ->map(fn($q) => [
                'registration' => $q->car_registration,
                'make' => $q->car_make,
                'model' => $q->car_model,
                'year' => $q->car_year,
                'type' => $q->vehicle_type,
            ]);

        return response()->json(['suggestions' => $suggestions]);
    }

    public function show(Claim $claim)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('super-admin') || $user->hasRole('admin');

        if (!$isAdmin && $claim->user_id !== $user->id && $claim->on_behalf_of_user_id !== $user->id) {
            abort(403);
        }

        $claim->load(['user', 'onBehalfOfUser']);

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
