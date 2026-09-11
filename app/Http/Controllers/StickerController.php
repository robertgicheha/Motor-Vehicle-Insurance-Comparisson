<?php

namespace App\Http\Controllers;

use App\Jobs\ScanDocumentJob;
use App\Mail\CertificateIssuedMail;
use App\Models\CertificateDocument;
use App\Models\Company;
use App\Models\Quotation;
use App\Models\Sticker;
use App\Models\StickerEmailLog;
use App\Services\AuditService;
use App\Services\ClamAVService;
use App\Services\FileSecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StickerController extends Controller
{
    public function issueForm(Request $request)
    {
        $quotation = null;
        if ($request->quotation_id) {
            $quotation = Quotation::with(['company', 'stickers' => function ($q) {
                $q->where('status', 'pending')->with('certificateDocuments');
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
                $q->where('status', 'pending')
                  ->with('certificateDocuments');
            }])
            ->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(quotation_number) LIKE ?', ["%{$search}%"])
                    ->orWhereLikeCI('car_registration', $search)
                    ->orWhereRaw('LOWER(first_name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search}%"])
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                          ->orWhereRaw('LOWER(first_name) LIKE ?', ["%{$search}%"])
                          ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search}%"]);
                    });
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
                    'customer_name' => $q->customer_name,
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
                    'existing_documents' => $q->stickers->first()?->certificateDocuments->map(fn($d) => [
                        'id' => $d->id,
                        'document_type' => $d->document_type,
                        'document_label' => $d->document_label,
                        'scan_status' => $d->scan_status,
                        'filename' => $d->filename,
                    ])->toArray() ?? [],
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
            'customer_name' => $quotation->customer_name,
            'customer_email' => $quotation->customer_email,
            'customer_phone' => $quotation->customer_phone,
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
    /**
     * True once every required KYC slot for this sticker's client type has
     * at least one document. Shared by uploadDocument()'s response,
     * scanStatus(), and issue()'s stricter (all-clean) gate, so the three
     * can never disagree about which slots corporate vs. individual needs.
     */
    private function stickerAllRequiredUploaded(Sticker $sticker, $documents): bool
    {
        if ($sticker->client_type === 'corporate') {
            return $documents->contains('document_type', 'kra_pin')
                && $documents->contains('document_type', 'certificate_of_incorporation')
                && $documents->contains('document_type', 'logbook_import');
        }

        $logbookSatisfied = $sticker->logbook_na
            ? $documents->contains('document_type', 'other')
            : $documents->contains('document_type', 'logbook');

        return $documents->contains('document_type', 'id_copy')
            && $documents->contains('document_type', 'kra_pin')
            && $logbookSatisfied;
    }

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

    public function uploadDocument(Request $request)
    {
        $request->validate([
            'quotation_id' => 'required|exists:quotations,id',
            'document_type' => 'required|in:id_copy,kra_pin,logbook,certificate_of_incorporation,logbook_import,other',
            // Only meaningful (and required) for the open-ended "other" slot —
            // the three fixed types already have an implicit label.
            'document_label' => 'required_if:document_type,other|nullable|string|max:255',
            'document' => 'required|file|mimes:pdf,jpg,jpeg|max:5120',
        ]);

        $quotation = Quotation::with('company')->findOrFail($request->quotation_id);
        $user = auth()->user();

        if (!$user->hasRole('super-admin') && !$user->hasRole('admin') && $quotation->user_id !== $user->id) {
            return response()->json(['error' => 'You do not have access to this quotation.'], 403);
        }

        $file = $request->file('document');

        // Extension/MIME/magic-byte check runs here, before the file ever
        // touches disk or the database — rejects a disguised executable
        // immediately instead of only catching it later when ScanDocumentJob
        // (which re-checks this plus the ClamAV malware scan) gets to it.
        $securityResult = (new FileSecurityService())->validate($file, $request->document_type);
        if (!$securityResult['valid']) {
            AuditService::activity('kyc_document_rejected', "KYC document upload rejected at intake: " . implode('; ', $securityResult['errors']), 'warning');
            return response()->json([
                'error' => 'This file could not be accepted: ' . implode(' ', $securityResult['errors']),
            ], 422);
        }

        $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '', $file->getClientOriginalName());
        // Stored on the private 'local' disk, never 'public' — a KYC document
        // must never be reachable via a direct, unauthenticated URL. See
        // downloadDocument() for the only (authenticated) way to read one back.
        $path = $file->storeAs('certificate-documents', $filename, 'local');

        $sticker = $this->findOrCreatePendingSticker($quotation, $user);

        // id_copy/kra_pin/logbook are single slots — a re-upload replaces
        // whatever's there. "other" has no slot to replace: each row on the
        // form is its own independent document (e.g. "Driver's Licence" and
        // "Insurance Cover Note" can both exist at once), so it always inserts.
        $existingDoc = $request->document_type !== 'other'
            ? CertificateDocument::where('sticker_id', $sticker->id)
                ->where('document_type', $request->document_type)
                ->first()
            : null;

        if ($existingDoc) {
            Storage::disk('local')->delete($existingDoc->path);
            $existingDoc->update([
                'filename' => $filename,
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'scan_status' => 'pending',
                'scan_result' => null,
                'uploaded_by' => $user->id,
            ]);
            $documentId = $existingDoc->id;
        } else {
            $doc = CertificateDocument::create([
                'sticker_id' => $sticker->id,
                'document_type' => $request->document_type,
                'document_label' => $request->document_type === 'other' ? $request->document_label : null,
                'filename' => $filename,
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'scan_status' => 'pending',
                'scan_result' => null,
                'uploaded_by' => $user->id,
            ]);
            $documentId = $doc->id;
        }

        // Real virus/malware scan, run asynchronously — the frontend already
        // polls scanStatus() for 'pending'/'scanning' documents, so this just
        // has to set the status and let ScanDocumentJob resolve it.
        ScanDocumentJob::dispatch($documentId);

        AuditService::log('create', 'CertificateDocument', $sticker->id, null, [
            'document_type' => $request->document_type,
            'document_label' => $request->document_label,
            'scan_status' => 'pending',
        ]);
        AuditService::activity('kyc_document_uploaded', "KYC document '" . ($request->document_label ?: $request->document_type) . "' uploaded for sticker {$sticker->sticker_number}");

        $documents = CertificateDocument::where('sticker_id', $sticker->id)->get();
        $allUploaded = $this->stickerAllRequiredUploaded($sticker, $documents);
        $allClean = $documents->every(fn($d) => $d->scan_status === 'clean');

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded — scanning in progress.',
            'scan_status' => 'pending',
            'document_type' => $request->document_type,
            'document_label' => $request->document_label,
            'all_uploaded' => $allUploaded,
            'all_clean' => $allClean,
            'can_issue' => $allUploaded && $allClean,
            'sticker_number' => $sticker->sticker_number,
            'sticker_id' => $sticker->id,
            'filename' => $filename,
            'document_id' => $documentId,
        ]);
    }

    /**
     * Removes one "other" document row (the fixed id_copy/kra_pin/logbook
     * slots are removed by simply re-uploading over them, so this endpoint
     * only needs to handle the open-ended list).
     */
    public function removeDocument(Request $request)
    {
        $request->validate([
            'document_id' => 'required|exists:certificate_documents,id',
        ]);

        $doc = CertificateDocument::findOrFail($request->document_id);
        $sticker = $doc->sticker;
        $user = auth()->user();

        if ($sticker && !$user->hasRole('super-admin') && !$user->hasRole('admin') && $sticker->issued_by !== $user->id) {
            return response()->json(['error' => 'You do not have access to this document.'], 403);
        }

        Storage::disk('local')->delete($doc->path);
        $doc->delete();

        return response()->json(['success' => true]);
    }

    public function scanStatus(Request $request)
    {
        $request->validate([
            'sticker_id' => 'required|exists:stickers,id',
        ]);

        $sticker = Sticker::find($request->sticker_id);
        if (!$sticker) {
            $sticker = Sticker::where('quotation_id', $request->sticker_id)->where('status', 'pending')->first();
        }
        if (!$sticker) {
            return response()->json(['success' => false, 'error' => 'No pending sticker found.'], 404);
        }
        $documents = CertificateDocument::where('sticker_id', $sticker->id)->get();

        $docs = $documents->map(fn($d) => [
            'id' => $d->id,
            'document_type' => $d->document_type,
            'document_label' => $d->document_label,
            'scan_status' => $d->scan_status,
            'scan_result' => $d->scan_result,
            'filename' => $d->filename,
        ]);

        $allUploaded = $this->stickerAllRequiredUploaded($sticker, $documents);
        $allClean = $documents->every(fn($d) => $d->scan_status === 'clean');
        $anyInfected = $documents->contains('scan_status', 'infected');

        return response()->json([
            'success' => true,
            'documents' => $docs,
            'client_type' => $sticker->client_type,
            'logbook_na' => $sticker->logbook_na,
            'all_uploaded' => $allUploaded,
            'all_clean' => $allClean,
            'any_infected' => $anyInfected,
            'can_issue' => $allUploaded && $allClean,
            'sticker_number' => $sticker->sticker_number,
        ]);
    }

    public function issue(Request $request)
    {
        $request->validate([
            'quotation_id' => 'required|exists:quotations,id',
            'admin_notes' => 'nullable|string|max:2000',
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
            return response()->json(['error' => 'No pending certificate found for this quotation. Please upload KYC documents first.'], 404);
        }

        $documents = CertificateDocument::where('sticker_id', $sticker->id)->get();

        // Corporate clients have their own fixed KYC set — KRA PIN,
        // Certificate of Incorporation, and Logbook/Import Documents (no N/A
        // concept; that only makes sense for an individual's own vehicle).
        // Individual clients keep the original id_copy/kra_pin/logbook set,
        // with logbook_na's "other document instead" fallback. The freeform
        // "Others" panel is optional extra material for both client types,
        // never required.
        if ($sticker->client_type === 'corporate') {
            $requiredTypes = ['kra_pin', 'certificate_of_incorporation', 'logbook_import'];
            $needsOther = false;
        } else {
            $requiredTypes = $sticker->logbook_na ? ['id_copy', 'kra_pin'] : ['id_copy', 'kra_pin', 'logbook'];
            $needsOther = $sticker->logbook_na;
        }

        $uploadedTypes = $documents->pluck('document_type')->toArray();
        $missingTypes = array_diff($requiredTypes, $uploadedTypes);

        if ($needsOther && !$documents->contains('document_type', 'other')) {
            $missingTypes[] = 'other (at least one alternate document is required since the logbook is marked N/A)';
        }

        if (!empty($missingTypes)) {
            return response()->json([
                'error' => 'Missing required documents: ' . implode(', ', $missingTypes),
                'missing' => $missingTypes,
            ], 422);
        }

        $infectedDocs = $documents->filter(fn($d) => $d->scan_status === 'infected');
        if ($infectedDocs->isNotEmpty()) {
            return response()->json([
                'error' => 'Some documents failed virus scan. Please re-upload clean files.',
                'infected' => $infectedDocs->pluck('document_type'),
            ], 422);
        }

        $fromAddress = $this->stickerEmailFromAddress();
        $fromName = $this->stickerEmailFromName();
        $ccAddress = $this->stickerEmailCc($quotation->company);

        $sticker->update([
            'status' => 'issued',
            'admin_notes' => $request->admin_notes,
            'email_subject' => 'Sticker Issuance Request - ' . $sticker->sticker_number,
            'email_from_address' => $fromAddress,
            'email_from_name' => $fromName,
            'reply_to_address' => $fromAddress,
        ]);

        $quotation->update(['status' => 'issued']);

        $kycPaths = $documents->map(fn($d) => storage_path('app/private/' . $d->path))->toArray();

        $emailStatus = 'queued';
        $emailError = null;
        $emailVia = config('mail.default');

        try {
            $mailable = new CertificateIssuedMail($sticker, $kycPaths, $ccAddress, $fromAddress, $fromName);

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

            // KYC documents are never retained on the system — the email
            // carrying them to the underwriter having actually sent is our
            // assurance they were attached successfully, so the files are
            // deleted immediately rather than kept for any grace period.
            // The CertificateDocument row (type, filename, scan result) is
            // kept for audit; only the file itself and its 'path' are removed.
            foreach ($documents as $document) {
                if ($document->path) {
                    Storage::disk('local')->delete($document->path);
                    $document->update(['path' => null, 'purged_at' => now()]);
                }
            }
            AuditService::activity('kyc_documents_purged', "KYC document file(s) for sticker {$sticker->sticker_number} deleted immediately after successful dispatch to the underwriter.");
        } catch (\Exception $e) {
            $emailStatus = 'failed';
            $emailError = $e->getMessage();
            Log::error("Failed to send sticker issued email: " . $e->getMessage());

            $sticker->update([
                'last_email_status' => 'failed',
                'last_email_error' => $e->getMessage(),
            ]);
        }

        StickerEmailLog::create([
            'sticker_id' => $sticker->id,
            'type' => 'outbound_to_underwriter',
            'recipient' => $sticker->underwriter_email,
            'sender' => $fromAddress,
            'subject' => 'Sticker Issuance Request - ' . $sticker->sticker_number,
            'status' => $emailStatus,
            'error_message' => $emailError,
            'attachments' => ['kyc_documents' => count($kycPaths), 'certificate_pdf' => true],
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
        AuditService::activity('kyc_documents_submitted', "KYC documents submitted for sticker {$sticker->sticker_number}: " . $documents->pluck('document_type')->implode(', '));

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
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
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
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
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
     * quotation number, registration number, or customer name, with no
     * login required (mirrors how a sticker number is independently
     * verifiable). If the visitor is logged in and owns the matched
     * quotation (or is staff), the response flags that they can jump
     * straight into sticker issuance without re-searching there.
     */
    public function searchQuotationPublic(Request $request)
    {
        $request->validate(['search' => 'required|string|min:2|max:100']);

        $search = trim($request->search);

        $quotation = Quotation::with(['company', 'user'])
            ->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(quotation_number) LIKE ?', ["%{$search}%"])
                    ->orWhereLikeCI('car_registration', $search)
                    ->orWhereRaw('LOWER(first_name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search}%"])
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                          ->orWhereRaw('LOWER(first_name) LIKE ?', ["%{$search}%"])
                          ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$search}%"]);
                    });
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
                'customer_name' => $quotation->customer_name,
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
     * chassis/engine number, or owner name — never by our internal Sticker ID
     * (that number never leaves this system, so nobody would be searching
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
                  ->orWhereLikeCI('customer_name', $search)
                  ->orWhereHas('quotation', function ($q) use ($search) {
                      $q->whereLikeCI('quotation_number', $search);
                  });
            })
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        if ($stickers->isEmpty()) {
            return response()->json(['success' => false, 'error' => 'No sticker found among your issued stickers. Search by quotation number, registration, chassis number, engine number, or owner name.']);
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
            'customer_name' => $sticker->customer_name,
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
