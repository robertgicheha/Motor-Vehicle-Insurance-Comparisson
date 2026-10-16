<?php

namespace App\Mail;

use App\Mail\Concerns\UsesEmailTemplate;
use App\Models\Sticker;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CertificateIssuedMail extends Mailable
{
    use Queueable, SerializesModels, UsesEmailTemplate;

    public Sticker $sticker;

    /**
     * KYC documents held entirely in memory, never on disk. Each entry is
     * ['name' => string, 'mime' => string, 'data' => string(binary)]. The
     * upload is scanned while still in Laravel's volatile request buffer, the
     * bytes are attached straight from memory (Attachment::fromData), and the
     * buffer is released when the request ends — the zero-storage lifecycle.
     *
     * @var array<int, array{name:string, mime:string, data:string}>
     */
    public array $kycAttachments;
    public ?string $ccAddress;
    public ?string $fromAddress;
    public ?string $fromName;
    public ?string $toOverride;

    public function __construct(Sticker $sticker, array $kycAttachments = [], ?string $ccAddress = null, ?string $fromAddress = null, ?string $fromName = null, ?string $toOverride = null)
    {
        $this->sticker = $sticker;
        $this->kycAttachments = $kycAttachments;
        $this->ccAddress = $ccAddress;
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
        $this->toOverride = $toOverride;
    }

    public function envelope(): Envelope
    {
        $subject = $this->resolveSubject();

        $ccAddresses = array_unique(array_filter([
            $this->ccAddress,
            config('services.sticker.mic_cc_email'),
        ]));

        $toAddress = $this->toOverride ?: $this->sticker->underwriter_email;

        return new Envelope(
            subject: $subject,
            from: $this->fromAddress ? new Address($this->fromAddress, $this->fromName ?: config('mail.from.name')) : null,
            to: $toAddress ? [$toAddress] : [],
            cc: $ccAddresses,
        );
    }

    public function content(): Content
    {
        $template = $this->template('certificate_issued');

        if ($template) {
            return $this->dynamicContent($template, $this->variables());
        }

        // The blade view also reads admin_notes / breakdown / issued_at, which are
        // already computed in variables(); without them the view throws
        // "Undefined variable" and the whole send fails. Merge them in rather than
        // duplicating the logic.
        return new Content(
            view: 'emails.certificate-issued',
            with: array_merge($this->variables(), [
                'sticker' => $this->sticker,
                'quotation' => $this->sticker->quotation,
            ]),
        );
    }

    protected function resolveSubject(): string
    {
        $variables = $this->variables();

        // Governed by the 'certificate_issued' email template subject, if an
        // admin has customized it; otherwise the fixed, spec'd subject.
        $template = $this->template('certificate_issued');
        if ($template && $template->subject) {
            return $template->renderContent($template->subject, $variables);
        }

        return 'Request for A Sticker';
    }

    protected function variables(): array
    {
        $quotation = $this->sticker->quotation;
        $breakdown = $this->sticker->breakdown ?: ($quotation?->breakdown ?: []);
        $isCorporate = $this->sticker->client_type === 'corporate';

        return [
            'sticker_number' => $this->sticker->sticker_number,
            'underwriter_name' => $this->sticker->company?->underwriter_name ?: 'Underwriter',
            'client_type' => $isCorporate ? 'corporate' : 'individual',
            'client_type_label' => $isCorporate ? 'Corporate' : 'Individual',
            'company' => $this->sticker->company?->name ?? 'N/A',
            'vehicle' => trim(($this->sticker->car_make ?? '') . ' ' . ($this->sticker->car_model ?? '')),
            'make' => $this->sticker->car_make ?: 'N/A',
            'model' => $this->sticker->car_model ?: 'N/A',
            'registration' => $this->sticker->vehicle_registration ?? 'N/A',
            'chassis_number' => $this->sticker->chassis_number ?: 'N/A',
            'engine_number' => $this->sticker->engine_number ?: 'N/A',
            'year' => $this->sticker->car_year,
            'valuation' => number_format($this->sticker->car_valuation, 2),
            'premium' => number_format($this->sticker->premium, 2),
            'cover_type' => $this->sticker->cover_type,
            'status' => ucfirst($this->sticker->status),
            'breakdown' => $breakdown,
            'issued_at' => $this->sticker->generated_at?->format('d M Y H:i') ?? now()->format('d M Y H:i'),
            'underwriter_email' => $this->sticker->underwriter_email,
            'admin_notes' => $this->sticker->admin_notes,
            'quotation_number' => $quotation?->quotation_number ?? 'N/A',
            'app_name' => config('app.name'),
        ];
    }

    public function attachments(): array
    {
        // The system never generates its own certificate/sticker PDF — this
        // email only requests the sticker; the underwriter replies to the
        // thread with the actual sticker and policy document. So only the
        // quotation (for their perusal) and the client's KYC documents go
        // out here.
        $attachments = [];

        if ($quotation = $this->sticker->quotation) {
            $quotation->loadMissing(['user', 'company']);
            $quotationPdf = Pdf::loadView('pdf.quotation', ['quotation' => $quotation]);
            $attachments[] = Attachment::fromData(
                fn() => $quotationPdf->output(),
                'quotation-' . str_replace(['/', '\\'], '-', $quotation->quotation_number) . '.pdf'
            )->withMime('application/pdf');
        }

        foreach ($this->kycAttachments as $attachment) {
            // Attached straight from the in-memory bytes captured during the
            // request — there is no file on disk to read back, which is the
            // whole point of the zero-storage lifecycle. An empty payload is
            // skipped (with a warning) rather than failing the whole send.
            $data = $attachment['data'] ?? '';
            if ($data === '') {
                \Illuminate\Support\Facades\Log::warning('Sticker email KYC attachment skipped (empty payload): ' . ($attachment['name'] ?? 'unknown'));

                continue;
            }

            $filename = $attachment['name'] ?? 'kyc-document';
            $mimeType = $attachment['mime'] ?? 'application/octet-stream';

            $attachments[] = Attachment::fromData(fn () => $data, $filename)
                ->withMime($mimeType);
        }

        return $attachments;
    }
}
