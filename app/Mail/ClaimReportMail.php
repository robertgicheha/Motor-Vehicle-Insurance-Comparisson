<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaimReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $label;
    public string $pdfContent;

    public function __construct(string $label, string $pdfContent)
    {
        $this->label = $label;
        $this->pdfContent = $pdfContent;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->label . ' - ' . now()->format('d M Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.claim-report',
            with: ['label' => $this->label],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn() => $this->pdfContent, str($this->label)->slug() . '-' . now()->format('Ymd') . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
