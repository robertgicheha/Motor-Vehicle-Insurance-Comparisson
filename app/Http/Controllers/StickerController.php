<?php

namespace App\Http\Controllers;

use App\Mail\CertificateIssuedMail;
use App\Models\CertificateDocument;
use App\Models\Company;
use App\Models\ConsentLog;
use App\Models\Quotation;
use App\Models\Sticker;
use App\Models\StickerEmailLog;
use App\Services\AuditService;
use App\Services\EsetScanService;
use App\Services\FileSecurityService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class StickerController extends Controller
{
    public function issueForm(Request $request)
    {
        $quotation = null;
        if ($request->quotation_id) {
            $quotation = Quotation::with(['company', 'stickers' => function ($q) {
                $q->where('status', 'pending');
            }])->findOrFail($request->quotation_id);
            $user = auth()->user();
            if (!$user->hasRole('super-admin') && !$user->hasRole('admin') && $quotation->user_id !== $user->id) {
                abort(403);
            }
        }

        $companies = Company::active()->get();

        return view('stickers.issue', compact('companies', 'quotation'));
    }

    public function searchQuotation(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:2|max:100',
        ]);

        $search = trim($request->search);

        $quotations = Quotation::with(['company', 'user'])
            ->with(['stickers' => function ($q) {
                $q->where('status', 'pending');
            }])
            ->where(function ($query) use ($search) {
                $query->whereLikeCI('quotation_number', $search)
                    ->orWhereLikeCI('car_registration', $search)
                    ->orWhereLikeCI('chassis_number', $search)
                    ->orWhereLikeCI('engine_number', $search);
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        $user = auth()->user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            $quotations = new \Illuminate\Pagination\LengthAwarePaginator(
                $quotations->getCollection()->filter(fn($q) => $q->user_id === $user->id)->values(),
                $quotations->total(),
                $quotations->perPage(),
                $quotations->currentPage(),
                ['path' => $quotations->path()]
            );
        }

        return response()->json([
            'success' => true,
            'quotations' => $quotations->map(function ($q) {
                return [
                    'id' => $q->id,
                    'quotation_number' => $q->quotation_number,
                    'company' => $q->company->name,
                    'company_id' => $q->company->id,
                    'car_make' => $q->car_make,
                    'car_model' => $q->car_model,
                    'car_year' => $q->car_year,
                    'car_registration' => $q->car_registration,
                    'car_valuation' => $q->car_valuation,
                    'premium' => $q->premium,
                    'pvt_premium' => $q->pvt_premium,
                    'total_premium' => $q->total_premium,
                    'excess_own_damage' => $q->excess_own_damage,
                    'excess_theft' => $q->excess_theft,
                    'vehicle_type' => $q->vehicle_type,
                    'num_passengers' => $q->num_passengers,
                    'status' => $q->status,
                    'created_at' => $q->created_at->format('d M Y'),
                    'underwriter_email' => $q->company->underwriter_email,
                    'breakdown' => $q->breakdown ?: [],
                    'sticker_id' => $q->stickers->first()?->id,
                    'client_type' => $q->stickers->first()?->client_type ?? 'individual',
                    'cover_start_date' => optional($q->stickers->first()?->cover_start_date)->format('Y-m-d'),
                    'cover_end_date' => optional($q->stickers->first()?->cover_end_date)->format('Y-m-d'),
                    'cover_duration_unit' => $q->stickers->first()?->cover_duration_unit,
                    'cover_duration_value' => $q->stickers->first()?->cover_duration_value,
                    'logbook_na' => (bool) ($q->stickers->first()?->logbook_na ?? false),
                    'cover_details_saved' => (bool) $q->stickers->first(),
                ];
            }),
            'total' => $quotations instanceof \Illuminate\Pagination\LengthAwarePaginator ? $quotations->total() : $quotations->count(),
            'current_page' => $quotations instanceof \Illuminate\Pagination\LengthAwarePaginator ? $quotations->currentPage() : 1,
        ]);
    }

    /**
     * The pending Sticker draft for a quotation is created lazily — the first
     * time the issuer either saves cover details or uploads a document,
     * whichever happens first. Both call sites need the exact same
     * create-if-missing logic, so it lives here once.
     */
    private function findOrCreatePendingSticker(Quotation $quotation, $user): Sticker
    {
        $sticker = Sticker::where('quotation_id', $quotation->id)
            ->where('status', 'pending')
            ->first();

        if ($sticker) {
            return $sticker;
        }

        return Sticker::create([
            'company_id' => $quotation->company_id,
            'quotation_id' => $quotation->id,
            'issued_by' => $user->id,
            'sticker_number' => Sticker::generateNumber($quotation->company),
            'vehicle_registration' => $quotation->car_registration,
            'car_make' => $quotation->car_make,
            'car_model' => $quotation->car_model,
            'car_year' => $quotation->car_year,
            'chassis_number' => $quotation->chassis_number,
            'engine_number' => $quotation->engine_number,
            'color' => $quotation->color,
            'car_valuation' => $quotation->car_valuation,
            'premium' => $quotation->total_premium,
            'premium_rate' => $quotation->breakdown['premium_rate'] ?? null,
            'cover_type' => $quotation->vehicle_type === 'psv' ? 'PSV' : 'Private',
            'status' => 'pending',
            'created_via' => 'web',
            'breakdown' => $quotation->breakdown ?: [],
            'underwriter_email' => $quotation->company->underwriter_email,
        ]);
    }

    /**
     * Saved before KYC upload is unlocked in the UI — the cover period and
     * whether the vehicle belongs to a corporate or individual client, both
     * of which the underwriter needs alongside the documents themselves.
     */
    /**
     * cover_end_date is a derived value, never typed in directly — computed
     * from the start date plus a duration (N weeks / N months / 1 year).
     * Centralised here so the live preview shown while filling the form and
     * the value actually persisted in saveCoverDetails() can never disagree.
     *
     * Weeks add exact days (7 * n) — matches how a "1 week" cover period is
     * meant to read: 09/09/2026 + 1 week = 16/09/2026. Months/years use
     * Carbon's *NoOverflow variants so a start date on the 29th–31st doesn't
     * skip into the following month (e.g. 31 Jan + 1 month lands on 28/29
     * Feb, not 3 Mar) — the accurate, unsurprising behaviour for a policy
     * period.
     */
    private function calculateCoverEnd(string $startDate, string $unit, int $value): \Carbon\Carbon
    {
        $start = \Carbon\Carbon::parse($startDate);

        $end = match ($unit) {
            'week' => $start->copy()->addDays($value * 7),
            'month' => $start->copy()->addMonthsNoOverflow($value),
            'year' => $start->copy()->addYearsNoOverflow($value),
        };

        // Inclusive end date: a cover period includes both its start and end
        // day, so 1 year from 09/08/2026 ends 08/08/2027, not 09/08/2027 —
        // standard policy-period convention.
        return $end->subDay();
    }

    public function calculateCoverEndDate(Request $request)
    {
        $request->validate([
            'cover_start_date' => 'required|date',
            'cover_duration_unit' => 'required|in:week,month,year',
            'cover_duration_value' => 'nullable|integer|min:1|max:52',
        ]);

        $unit = $request->cover_duration_unit;
        $value = $unit === 'year' ? 1 : (int) ($request->cover_duration_value ?: 1);

        if ($unit === 'week' && $value > 52) {
            $value = 52;
        }
        if ($unit === 'month' && $value > 12) {
            $value = 12;
        }

        $end = $this->calculateCoverEnd($request->cover_start_date, $unit, $value);

        return response()->json([
            'success' => true,
            'cover_end_date' => $end->format('Y-m-d'),
            'cover_end_date_display' => $end->format('d/m/Y'),
        ]);
    }

    public function saveCoverDetails(Request $request)
    {
        $request->validate([
            'quotation_id' => 'required|exists:quotations,id',
            'client_type' => 'required|in:individual,corporate',
            'cover_start_date' => 'required|date',
            'cover_duration_unit' => 'required|in:week,month,year',
            'cover_duration_value' => 'nullable|integer|min:1|max:52',
        ]);

        $quotation = Quotation::with('company')->findOrFail($request->quotation_id);
        $user = auth()->user();

        if (!$user->hasRole('super-admin') && !$user->hasRole('admin') && $quotation->user_id !== $user->id) {
            return response()->json(['error' => 'You do not have access to this quotation.'], 403);
        }

        $unit = $request->cover_duration_unit;
        $value = $unit === 'year' ? 1 : max(1, min($unit === 'week' ? 52 : 12, (int) ($request->cover_duration_value ?: 1)));

        // Recomputed server-side rather than trusting a client-submitted end
        // date — the same reasoning applied to underwriter emails and
        // customer identity elsewhere in this app: derived values are
        // recalculated from their real inputs, not accepted as given.
        $endDate = $this->calculateCoverEnd($request->cover_start_date, $unit, $value);

        $sticker = $this->findOrCreatePendingSticker($quotation, $user);
        $sticker->update([
            'client_type' => $request->client_type,
            'cover_start_date' => $request->cover_start_date,
            'cover_duration_unit' => $unit,
            'cover_duration_value' => $value,
            'cover_end_date' => $endDate,
        ]);

        return response()->json([
            'success' => true,
            'sticker_id' => $sticker->id,
            'cover_end_date' => $endDate->format('Y-m-d'),
            'cover_end_date_display' => $endDate->format('d/m/Y'),
        ]);
    }

    /**
     * Toggles whether the logbook is genuinely unavailable for this vehicle
     * (new registration, financed/pending transfer, etc). When set, issue()
     * accepts one or more "other" documents in its place instead of blocking
     * on a document that may not exist yet.
     */
    public function toggleLogbookNA(Request $request)
    {
        $request->validate([
            'quotation_id' => 'required|exists:quotations,id',
            'logbook_na' => 'required|boolean',
        ]);

        $quotation = Quotation::with('company')->findOrFail($request->quotation_id);
        $user = auth()->user();

        if (!$user->hasRole('super-admin') && !$user->hasRole('admin') && $quotation->user_id !== $user->id) {
            return response()->json(['error' => 'You do not have access to this quotation.'], 403);
        }

        $sticker = $this->findOrCreatePendingSticker($quotation, $user);
        $sticker->update(['logbook_na' => $request->boolean('logbook_na')]);

        return response()->json([
            'success' => true,
            'sticker_id' => $sticker->id,
            'logbook_na' => $sticker->logbook_na,
        ]);
    }

    /**
     * Validate + malware-scan one in-memory upload and produce both the email
     * attachment payload (['name','mime','data']) and the audit row that
     * outlives the bytes. Nothing here touches disk: the file lives in
     * Laravel's volatile request buffer, ESET scans it there, and the bytes
     * are read straight into memory for the email attachment.
     *
     * @return array{error?:string, document_type?:string, rejected?:bool, attachment?:array{name:string,mime:string,data:string}, audit?:array<string,mixed>}
     */
    private function screenKycFile(UploadedFile $file, string $documentType, FileSecurityService $security, EsetScanService $eset): array
    {
        // Extension/MIME/magic-byte check runs first, while the file is still
        // only a request buffer — a disguised executable is rejected here.
        $securityResult = $security->validate($file, $documentType);
        if (!$securityResult['valid']) {
            AuditService::activity('kyc_document_rejected', "KYC document rejected at intake ({$documentType}): " . implode('; ', $securityResult['errors']), 'warning');

            return [
                'error' => 'This file could not be accepted: ' . implode(' ', $securityResult['errors']),
                'document_type' => $documentType,
                'rejected' => true,
            ];
        }

        // ESET Server Security (odscan) scans the file in transit. An infected
        // file is rejected here — it is never attached to any email and never
        // written anywhere. A scanner that is unavailable degrades to
        // "passed intake checks only" (see EsetScanService), matching how the
        // upload endpoints behaved before.
        $scan = $eset->scan($file->getRealPath());
        if ($scan['status'] === EsetScanService::STATUS_INFECTED) {
            AuditService::activity('kyc_document_rejected', "KYC document rejected by ESET ({$documentType}): {$scan['result']}", 'warning');

            return [
                'error' => 'Security threat detected! The uploaded file is unsafe. Please upload a clean file.',
                'document_type' => $documentType,
                'rejected' => true,
            ];
        }

        $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '', $file->getClientOriginalName());

        return [
            'attachment' => [
                'name' => $filename,
                'mime' => $file->getClientMimeType() ?: 'application/octet-stream',
                'data' => (string) file_get_contents($file->getRealPath()),
            ],
            'audit' => [
                'filename' => $filename,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'scan_status' => 'clean',
                'scan_result' => $scan['result'],
            ],
        ];
    }

    public function issue(Request $request)
    {
        $request->validate([
            'quotation_id' => 'required|exists:quotations,id',
            'admin_notes' => 'nullable|string|max:2000',
            'consent' => ['accepted'],
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg|max:5120',
            'other_documents' => 'nullable|array',
            'other_documents.*' => 'file|mimes:pdf,jpg,jpeg|max:5120',
            'other_labels' => 'nullable|array',
            'other_labels.*' => 'nullable|string|max:255',
        ]);

        $quotation = Quotation::with('company')->findOrFail($request->quotation_id);
        $user = auth()->user();

        if (!$user->hasRole('super-admin') && !$user->hasRole('admin') && $quotation->user_id !== $user->id) {
            return response()->json(['error' => 'You do not have access to this quotation.'], 403);
        }

        $sticker = Sticker::where('quotation_id', $quotation->id)
            ->where('status', 'pending')
            ->first();

        if (!$sticker) {
            return response()->json(['error' => 'No pending cover details found for this quotation. Please save the cover details first.'], 404);
        }

        // The client holds every KYC file in browser memory until this single
        // request — nothing was persisted, so this request is the first (and
        // only) time the server ever sees the bytes. They arrive in the
        // request's volatile buffer, are screened and scanned here, then read
        // straight into memory for the email attachment; the buffer is
        // released when the request ends.
        $allowedFixed = ['id_copy', 'kra_pin', 'logbook', 'certificate_of_incorporation', 'logbook_import'];

        $fixedUploads = collect($request->file('documents') ?? [])
            ->filter(fn ($file) => $file instanceof UploadedFile)
            ->only($allowedFixed);

        $otherUploads = collect($request->file('other_documents') ?? [])
            ->filter(fn ($file) => $file instanceof UploadedFile);

        // Required KYC set for this client type — corporate clients have their
        // own fixed set (KRA PIN, Certificate of Incorporation, Logbook/Import
        // Documents); individual clients keep id_copy/kra_pin/logbook, with
        // logbook_na's "other document instead" fallback. The freeform "Others"
        // panel is optional extra material for both, never required, unless it
        // is standing in for an N/A logbook.
        if ($sticker->client_type === 'corporate') {
            $requiredTypes = ['kra_pin', 'certificate_of_incorporation', 'logbook_import'];
            $needsOther = false;
        } else {
            $requiredTypes = $sticker->logbook_na ? ['id_copy', 'kra_pin'] : ['id_copy', 'kra_pin', 'logbook'];
            $needsOther = $sticker->logbook_na;
        }

        $missingTypes = array_values(array_diff($requiredTypes, $fixedUploads->keys()->all()));

        if ($needsOther && $otherUploads->isEmpty()) {
            $missingTypes[] = 'other (at least one alternate document is required since the logbook is marked N/A)';
        }

        if (!empty($missingTypes)) {
            return response()->json([
                'error' => 'Missing required documents: ' . implode(', ', $missingTypes),
                'missing' => $missingTypes,
            ], 422);
        }

        // Screen + scan every submitted file BEFORE any state change or email.
        // A single rejected file aborts the whole request — no sticker is
        // marked issued, nothing is recorded, and no email is dispatched.
        $security = new FileSecurityService();
        $eset = new EsetScanService();

        $kycAttachments = [];
        $auditRows = [];

        foreach ($fixedUploads as $type => $file) {
            $screened = $this->screenKycFile($file, $type, $security, $eset);
            if (isset($screened['error'])) {
                return response()->json($screened, 422);
            }
            $kycAttachments[] = $screened['attachment'];
            $auditRows[] = $screened['audit'] + ['document_type' => $type, 'document_label' => null];
        }

        foreach ($otherUploads as $index => $file) {
            $label = trim((string) $request->input("other_labels.{$index}")) ?: 'Supporting document';

            $screened = $this->screenKycFile($file, 'other', $security, $eset);
            if (isset($screened['error'])) {
                return response()->json($screened, 422);
            }
            $kycAttachments[] = $screened['attachment'];
            $auditRows[] = $screened['audit'] + ['document_type' => 'other', 'document_label' => $label];
        }

        $fromAddress = $this->stickerEmailFromAddress();
        $fromName = $this->stickerEmailFromName();
        $ccAddress = $this->stickerEmailCc($quotation->company);

        // The consent record is written in the same transaction as the sticker
        // request state change. If it cannot be saved, the whole transaction
        // is rolled back and the underwriter email — sent only after this
        // block — is never dispatched.
        try {
            DB::transaction(function () use ($sticker, $quotation, $request, $fromAddress, $fromName, $auditRows, $user) {
                $sticker->update([
                    'status' => 'issued',
                    'admin_notes' => $request->admin_notes,
                    'email_subject' => 'Sticker Issuance Request - ' . $sticker->sticker_number,
                    'email_from_address' => $fromAddress,
                    'reply_to_address' => $fromAddress,
                ]);

                $quotation->update(['status' => 'issued']);

                ConsentLog::create([
                    'sticker_id' => $sticker->id,
                    'consent_given' => true,
                    'consent_version' => config('consent.version'),
                    'consent_text' => config('consent.sticker_text'),
                    'clause_reference' => config('consent.clause'),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'consented_at' => now(),
                ]);

                // Audit-only rows: the metadata that outlives the bytes (type,
                // original filename, MIME, size, scan result). 'path' is always
                // null and purged_at is stamped now — there was never a file to
                // purge, the bytes only ever existed in the request buffer.
                foreach ($auditRows as $row) {
                    CertificateDocument::create([
                        'sticker_id' => $sticker->id,
                        'document_type' => $row['document_type'],
                        'document_label' => $row['document_label'],
                        'filename' => $row['filename'],
                        'path' => null,
                        'mime_type' => $row['mime_type'],
                        'file_size' => $row['file_size'],
                        'scan_status' => $row['scan_status'],
                        'scan_result' => $row['scan_result'],
                        'uploaded_by' => $user->id,
                        'purged_at' => now(),
                    ]);
                }
            });
        } catch (\Throwable $e) {
            Log::error("Failed to record consent for sticker {$sticker->sticker_number}: " . $e->getMessage());

            return response()->json(['error' => 'Could not record your consent. Please try again.'], 500);
        }

        $emailStatus = 'queued';
        $emailError = null;
        $emailVia = config('mail.default');

        try {
            // KYC bytes are attached straight from memory (Attachment::fromData
            // inside the mailable). There is no path to read back and nothing
            // on disk to purge afterwards — the whole point of the zero-storage
            // lifecycle.
            $mailable = new CertificateIssuedMail($sticker, $kycAttachments, $ccAddress, $fromAddress, $fromName);

            Mail::send($mailable);

            $emailStatus = 'sent';
            $sticker->update([
                'last_email_status' => 'sent',
                'email_sent_at' => now(),
            ]);

            $admins = \App\Models\User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['super-admin', 'admin']);
            })->get();

            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\StickerCompletedReminder($sticker));
            }
        } catch (\Exception $e) {
            $emailStatus = 'failed';
            $emailError = $e->getMessage();
            Log::error("Failed to send sticker issued email: " . $e->getMessage());

            $sticker->update([
                'last_email_status' => 'failed',
                'last_email_error' => $e->getMessage(),
            ]);
        } finally {
            // Drop the in-memory copies of the KYC bytes as soon as the send
            // attempt is over; the OS reclaims the request buffer at the end of
            // the request regardless.
            $kycAttachments = [];
            gc_collect_cycles();
        }

        StickerEmailLog::create([
            'sticker_id' => $sticker->id,
            'type' => 'outbound_to_underwriter',
            'subject' => 'Sticker Issuance Request - ' . $sticker->sticker_number,
            'status' => $emailStatus,
            'error_message' => $emailError,
            'attachments' => ['kyc_documents' => count($auditRows), 'certificate_pdf' => true],
            'metadata' => ['cc' => $ccAddress, 'via' => $emailVia],
            'triggered_by' => $user->id,
            'sent_at' => $emailStatus === 'sent' ? now() : null,
        ]);

        AuditService::log('update', 'Sticker', $sticker->id, ['status' => 'pending'], [
            'status' => 'issued',
            'email_sent_at' => now()->toISOString(),
            'turnaround_deadline' => now()->addMinutes(config('services.sticker.turnaround_minutes', 30))->toISOString(),
        ]);
        AuditService::activity('sticker_request', "Sticker {$sticker->sticker_number} issued for quotation {$quotation->quotation_number} to underwriter {$sticker->underwriter_email}. Turnaround deadline: " . now()->addMinutes(config('services.sticker.turnaround_minutes', 30))->format('d M Y H:i'));
        AuditService::activity('consent_captured', "DPA consent (" . config('consent.clause') . ", version " . config('consent.version') . ") captured for sticker {$sticker->sticker_number}.");
        AuditService::activity('kyc_documents_submitted', "KYC documents submitted (in-memory, zero-storage) for sticker {$sticker->sticker_number}: " . collect($auditRows)->pluck('document_type')->implode(', '));

        return response()->json([
            'success' => true,
            'message' => 'Sticker request submitted successfully! The underwriter has been notified.',
            'sticker_number' => $sticker->sticker_number,
            'redirect_url' => route('stickers.show', $sticker),
        ]);
    }

    public function create(Request $request)
    {
        $quotation = null;
        if ($request->quotation_id) {
            $quotation = Quotation::with('company')->find($request->quotation_id);
        }
        $companies = Company::active()->get();
        return view('stickers.generate', compact('companies', 'quotation'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quotation_id' => 'nullable|exists:quotations,id',
            'company_id' => 'required|exists:companies,id',
            'vehicle_registration' => 'nullable|string|max:50',
            'car_make' => 'required|string|max:255',
            'car_model' => 'required|string|max:255',
            'car_year' => 'required|integer|min:1990|max:2030',
            'chassis_number' => 'nullable|string|max:100',
            'engine_number' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'car_valuation' => 'required|numeric|min:800000|max:1300000',
            'premium' => 'required|numeric',
            'premium_rate' => 'nullable|numeric|max:100',
            'cover_type' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $company = Company::findOrFail($request->company_id);

        $sticker = Sticker::create([
            'company_id' => $request->company_id,
            'quotation_id' => $request->quotation_id,
            'issued_by' => auth()->id(),
            'sticker_number' => Sticker::generateNumber($company),
            'vehicle_registration' => $request->vehicle_registration,
            'car_make' => $request->car_make,
            'car_model' => $request->car_model,
            'car_year' => $request->car_year,
            'chassis_number' => $request->chassis_number,
            'engine_number' => $request->engine_number,
            'color' => $request->color,
            'car_valuation' => $request->car_valuation,
            'premium' => $request->premium,
            'premium_rate' => $request->premium_rate,
            'cover_type' => $request->cover_type ?? 'Comprehensive',
            'status' => 'completed',
            'created_via' => 'web',
            'breakdown' => $request->breakdown ?? [],
            'generated_at' => now(),
        ]);

        if ($request->quotation_id) {
            $quotation = Quotation::find($request->quotation_id);
            if ($quotation) {
                $quotation->update(['status' => 'completed']);
                AuditService::activity('quotation_completed', "Quotation {$quotation->quotation_number} marked completed (certificate {$sticker->sticker_number} issued)");
            }
        }

        AuditService::log('create', 'Sticker', $sticker->id, null, $sticker->toArray());
        AuditService::activity('sticker_generated', "Sticker {$sticker->sticker_number} generated via web for {$company->name}");

        return redirect()->route('stickers.show', $sticker)
            ->with('success', "Certificate {$sticker->sticker_number} generated successfully!");
    }

    public function show(Sticker $sticker)
    {
        $sticker->load(['company', 'issuedBy', 'certificateDocuments']);
        return view('stickers.show', compact('sticker'));
    }

    public function userLookup()
    {
        $individualReports = collect();
        $groupReports = collect();
        $individualStats = ['total' => 0, 'pending' => 0, 'completed' => 0, 'total_premium' => 0];
        $individualMonthly = collect();
        $individualByCompany = collect();
        $isAdmin = false;
        $user = auth()->user();

        if ($user) {
            $isAdmin = $user->hasRole('super-admin') || $user->hasRole('admin');

            $allMine = Sticker::with(['company', 'quotation'])
                ->where('issued_by', $user->id)
                ->latest()
                ->get();

            $individualReports = $allMine->take(20)->values();

            $individualStats = [
                'total' => $allMine->count(),
                'completed' => $allMine->where('status', 'completed')->count(),
                'pending' => $allMine->where('status', '!=', 'completed')->count(),
                'total_premium' => $allMine->sum('premium'),
            ];

            $individualMonthly = $allMine
                ->groupBy(fn($s) => $s->created_at->format('M Y'))
                ->map->count()
                ->reverse()
                ->take(6)
                ->reverse();

            $individualByCompany = $allMine
                ->groupBy(fn($s) => $s->company->name ?? 'Unknown')
                ->map->count()
                ->sortDesc();

            if ($isAdmin) {
                $groupReports = Sticker::with('issuedBy:id,name')
                    ->whereNotNull('issued_by')
                    ->get()
                    ->groupBy('issued_by')
                    ->map(function ($stickers) {
                        return [
                            'user' => $stickers->first()->issuedBy,
                            'total' => $stickers->count(),
                            'completed' => $stickers->where('status', 'completed')->count(),
                            'pending' => $stickers->where('status', '!=', 'completed')->count(),
                            'total_premium' => $stickers->sum('premium'),
                            'latest_at' => $stickers->max('created_at'),
                        ];
                    })
                    ->sortByDesc('total')
                    ->values();
            }
        }

        return view('stickers.user-lookup', compact(
            'individualReports', 'groupReports', 'isAdmin',
            'individualStats', 'individualMonthly', 'individualByCompany'
        ));
    }

    public function lookupByQuotation(Request $request)
    {
        $request->validate(['quotation_number' => 'required|string']);

        $quotation = Quotation::with('company')
            ->where('quotation_number', $request->quotation_number)
            ->first();

        if (!$quotation) {
            return response()->json(['error' => 'No quotation found with that number.'], 404);
        }

        $user = auth()->user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin') && $quotation->user_id !== $user->id) {
            return response()->json(['error' => 'You do not have access to this quotation.'], 403);
        }

        $sticker = Sticker::where('quotation_id', $quotation->id)->first();

        return response()->json([
            'quotation' => [
                'id' => $quotation->id,
                'number' => $quotation->quotation_number,
                'company' => $quotation->company->name,
                'car_make' => $quotation->car_make,
                'car_model' => $quotation->car_model,
                'car_year' => $quotation->car_year,
                'car_registration' => $quotation->car_registration,
                'car_valuation' => $quotation->car_valuation,
                'total_premium' => $quotation->total_premium,
                'status' => $quotation->status,
                'created_at' => $quotation->created_at->format('d M Y'),
            ],
            'sticker' => $sticker ? [
                'id' => $sticker->id,
                'number' => $sticker->sticker_number,
                'download_url' => route('stickers.download', $sticker),
                'print_url' => route('stickers.print', $sticker),
                'show_url' => route('stickers.show', $sticker),
            ] : null,
            'can_generate' => !$sticker,
        ]);
    }

    public function lookup()
    {
        return view('stickers.lookup');
    }

    public function search(Request $request)
    {
        $request->validate(['number' => 'required|string']);

        $sticker = Sticker::where('sticker_number', $request->number)->first();

        if (!$sticker) {
            return back()->with('error', 'No certificate found with that number.');
        }

        return redirect()->route('stickers.show', $sticker);
    }

    /**
     * Public quotation lookup for the "Look Up Sticker" page — search by
     * quotation number, registration number, chassis number, or engine
     * number, with no login required (mirrors how a sticker number is
     * independently verifiable). If the visitor is logged in and owns the
     * matched quotation (or is staff), the response flags that they can jump
     * straight into sticker issuance without re-searching there.
     */
    public function searchQuotationPublic(Request $request)
    {
        $request->validate(['search' => 'required|string|min:2|max:100']);

        $search = trim($request->search);

        $quotation = Quotation::with(['company', 'user'])
            ->where(function ($query) use ($search) {
                $query->whereLikeCI('quotation_number', $search)
                    ->orWhereLikeCI('car_registration', $search)
                    ->orWhereLikeCI('chassis_number', $search)
                    ->orWhereLikeCI('engine_number', $search);
            })
            ->latest()
            ->first();

        if (!$quotation) {
            return response()->json(['error' => 'No quotation found matching your search.'], 404);
        }

        $sticker = Sticker::where('quotation_id', $quotation->id)->first();

        $user = auth()->user();
        $isOwner = $user && ($user->hasRole('super-admin') || $user->hasRole('admin') || $quotation->user_id === $user->id);

        return response()->json([
            'quotation' => [
                'id' => $quotation->id,
                'quotation_number' => $quotation->quotation_number,
                'company' => $quotation->company->name ?? 'Unknown',
                'company_logo' => $quotation->company?->logoUrl(),
                'car_make' => $quotation->car_make,
                'car_model' => $quotation->car_model,
                'car_year' => $quotation->car_year,
                'car_registration' => $quotation->car_registration,
                'car_valuation' => $quotation->car_valuation,
                'total_premium' => $quotation->total_premium,
                'status' => $quotation->status,
                'created_at' => $quotation->created_at->format('d M Y'),
            ],
            'sticker' => $sticker ? [
                'sticker_number' => $sticker->sticker_number,
                'status' => $sticker->status,
                'show_url' => route('stickers.show', $sticker),
            ] : null,
            'can_generate_sticker' => !$sticker && $isOwner,
            'requires_login' => !$user,
            'generate_url' => route('stickers.issue', ['quotation_id' => $quotation->id]),
        ]);
    }

    public function printView(Sticker $sticker)
    {
        $sticker->load('company');
        $sticker->increment('download_count');
        $sticker->update(['last_downloaded_at' => now()]);

        AuditService::activity('sticker_printed', "Sticker/certificate {$sticker->sticker_number} printed or downloaded");

        return view('stickers.print', compact('sticker'));
    }

    /**
     * The noreply sender address for underwriter emails, as configured in
     * Admin > Stickers > Settings > Email Configuration. Falls back to the
     * app's default mail-from address if nothing has been set yet.
     */
    protected function stickerEmailFromAddress(): string
    {
        return \App\Models\EmailSetting::where('key', 'sticker_email_from_address')->value('value')
            ?: config('mail.from.address');
    }

    protected function stickerEmailFromName(): string
    {
        return \App\Models\EmailSetting::where('key', 'sticker_email_from_name')->value('value')
            ?: config('mail.from.name');
    }

    /**
     * CC address for underwriter emails: the company's own CC address takes
     * priority, then the global Sticker Settings CC, then the app default.
     */
    protected function stickerEmailCc(?Company $company): ?string
    {
        return $company?->sticker_notification_emails
            ?: \App\Models\EmailSetting::where('key', 'sticker_email_cc')->value('value')
            ?: config('mail.from.address');
    }

    /**
     * "View All Stickers" for a regular user — the same shape as the admin
     * sticker dashboard, but scoped to only the stickers this user has
     * personally issued.
     */
    public function myStickers(Request $request)
    {
        $userId = auth()->id();

        $stickers = Sticker::with('company')
            ->where('issued_by', $userId)
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Sticker::where('issued_by', $userId)->count(),
            'pending' => Sticker::where('issued_by', $userId)->where('status', '!=', 'completed')->count(),
            'completed' => Sticker::where('issued_by', $userId)->where('status', 'completed')->count(),
            'total_premium' => Sticker::where('issued_by', $userId)->sum('premium'),
        ];

        return view('user.stickers.my-stickers', compact('stickers', 'stats'));
    }

    /**
     * "Update Stickers" for a regular user — mirrors the admin update-sticker
     * workflow (search a sticker, enter the underwriter's returned sticker
     * number, mark completed) but scoped to their own pending stickers only.
     */
    public function myUpdateForm(Request $request)
    {
        $stickers = Sticker::with(['quotation', 'company', 'issuedBy'])
            ->where('issued_by', auth()->id())
            ->where('status', 'issued')
            ->latest()
            ->paginate(20);

        return view('user.stickers.my-updates', compact('stickers'));
    }

    /**
     * Searches the caller's own issued stickers by whatever the underwriter's
     * email thread will actually reference — quotation number, registration,
     * chassis/engine number, or sticker number — never by our internal Sticker
     * ID (that number never leaves this system, so nobody would be searching
     * for it). Returns every match so the issuer can pick the right one
     * before recording the underwriter's real sticker number.
     */
    public function mySearchSticker(Request $request)
    {
        $request->validate(['search' => 'required|string|min:2']);

        $search = trim($request->search);
        $stickers = Sticker::with(['company', 'issuedBy', 'quotation'])
            ->where('issued_by', auth()->id())
            ->where(function ($q) use ($search) {
                $q->whereLikeCI('vehicle_registration', $search)
                  ->orWhereLikeCI('chassis_number', $search)
                  ->orWhereLikeCI('engine_number', $search)
                  ->orWhereLikeCI('sticker_number', $search)
                  ->orWhereLikeCI('underwriter_sticker_no', $search)
                  ->orWhereHas('quotation', function ($q) use ($search) {
                      $q->whereLikeCI('quotation_number', $search);
                  });
            })
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        if ($stickers->isEmpty()) {
            return response()->json(['success' => false, 'error' => 'No sticker found among your issued stickers. Search by quotation number, registration, chassis number, engine number, or sticker number.']);
        }

        return response()->json([
            'success' => true,
            'stickers' => $stickers->map(fn($sticker) => $this->stickerSearchPayload($sticker)),
        ]);
    }

    private function stickerSearchPayload(Sticker $sticker): array
    {
        $documents = CertificateDocument::where('sticker_id', $sticker->id)->get();

        return [
            'id' => $sticker->id,
            'sticker_number' => $sticker->sticker_number,
            'underwriter_sticker_no' => $sticker->underwriter_sticker_no,
            'quotation_number' => $sticker->quotation?->quotation_number,
            'vehicle_registration' => $sticker->vehicle_registration,
            'chassis_number' => $sticker->chassis_number,
            'engine_number' => $sticker->engine_number,
            'car_make' => $sticker->car_make,
            'car_model' => $sticker->car_model,
            'car_year' => $sticker->car_year,
            'premium' => $sticker->premium,
            'cover_type' => $sticker->cover_type,
            'status' => $sticker->status,
            'company' => $sticker->company->name ?? 'N/A',
            'issued_by' => $sticker->issuedBy?->name ?? 'N/A',
            'completed_by' => $sticker->completedBy?->name,
            'completed_at' => $sticker->generated_at?->format('d M Y H:i'),
            'is_completed' => $sticker->status === 'completed',
            'documents' => $documents->pluck('document_type', 'scan_status'),
        ];
    }

    public function myUpdateSticker(Request $request)
    {
        $request->validate([
            'sticker_id' => 'required|exists:stickers,id',
            'underwriter_sticker_no' => 'required|string|max:100',
        ]);

        $user = auth()->user();
        $sticker = Sticker::where('id', $request->sticker_id)->where('issued_by', $user->id)->first();

        if (!$sticker) {
            return response()->json(['success' => false, 'error' => 'Sticker not found among your issued stickers.'], 404);
        }

        if ($sticker->status === 'completed') {
            return response()->json([
                'success' => false,
                'error' => 'This sticker has already been completed by ' . ($sticker->completedBy?->name ?? 'Unknown') . ' on ' . $sticker->generated_at?->format('d M Y H:i') . '. It cannot be overridden.',
            ]);
        }

        $oldStatus = $sticker->status;
        $sticker->update([
            'status' => 'completed',
            'underwriter_sticker_no' => $request->underwriter_sticker_no,
            'generated_at' => now(),
            'completed_by' => $user->id,
        ]);

        AuditService::log('update', 'Sticker', $sticker->id, [
            'status' => $oldStatus,
            'completed_by' => null,
            'generated_at' => null,
        ], [
            'status' => 'completed',
            'completed_by' => $user->id,
            'generated_at' => now()->toISOString(),
        ]);
        AuditService::activity('sticker_completed', "Sticker {$sticker->sticker_number} marked as completed by {$user->name}. Certificate generated.");

        return response()->json([
            'success' => true,
            'message' => "Sticker {$sticker->sticker_number} marked as completed successfully.",
        ]);
    }
}
