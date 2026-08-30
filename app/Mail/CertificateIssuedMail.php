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
    public array $kycPaths;
    public ?string $ccAddress;
    public ?string $fromAddress;
    public ?string $fromName;
    public ?string $toOverride;

    public function __construct(Sticker $sticker, array $kycPaths = [], ?string $ccAddress = null, ?string $fromAddress = null, ?string $fromName = null, ?string $toOverride = null)
    {
        $this->sticker = $sticker;
        $this->kycPaths = $kycPaths;
        $this->ccAddress = $ccAddress;
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
        $this->toOverride = $toOverride;
    }

    public function envelope(): Envelope
    {
        $subject = $this->envelopeSubject($this->template('certificate_issued'), 'Sticker Issuance - ' . $this->sticker->sticker_number, $this->variables());

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

        return new Content(
            view: 'emails.certificate-issued',
            with: [
                'sticker' => $this->sticker,
                'quotation' => $this->sticker->quotation,
                'kycPaths' => $this->kycPaths,
            ],
        );
    }

    protected function variables(): array
    {
        $quotation = $this->sticker->quotation;
        $breakdown = $this->sticker->breakdown ?: ($quotation?->breakdown ?: []);

        return [
            'sticker_number' => $this->sticker->sticker_number,
            'customer_name' => $this->sticker->customer_name,
            'company' => $this->sticker->company?->name ?? 'N/A',
            'vehicle' => trim(($this->sticker->car_make ?? '') . ' ' . ($this->sticker->car_model ?? '')),
            'registration' => $this->sticker->vehicle_registration ?? 'N/A',
            'year' => $this->sticker->car_year,
            'valuation' => number_format($this->sticker->car_valuation, 2),
            'premium' => number_format($this->sticker->premium, 2),
            'cover_type' => $this->sticker->cover_type,
            'status' => ucfirst($this->sticker->status),
            'breakdown' => $breakdown,
            'issued_at' => $this->sticker->generated_at?->format('d M Y H:i') ?? now()->format('d M Y H:i'),
            'underwriter_email' => $this->sticker->underwriter_email,
            'admin_notes' => $this->sticker->admin_notes,
            'app_name' => config('app.name'),
        ];
    }

    public function attachments(): array
    {
        $attachments = [];

        $pdf = Pdf::loadView('pdf.sticker', ['sticker' => $this->sticker]);
        $attachments[] = Attachment::fromData(
            fn() => $pdf->output(),
            "certificate-{$this->sticker->sticker_number}.pdf"
        )->withMime('application/pdf');

        foreach ($this->kycPaths as $index => $path) {
            if (!file_exists($path)) {
                continue;
            }

            $filename = basename($path);
            $mimeType = mime_content_type($path) ?: 'application/octet-stream';

            $attachments[] = Attachment::fromPath($path)
                ->as($filename)
                ->withMime($mimeType);
        }

        return $attachments;
    }
}
