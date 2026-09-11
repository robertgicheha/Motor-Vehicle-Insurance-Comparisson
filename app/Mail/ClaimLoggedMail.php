<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaimLoggedMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $claim;
    public array $attachmentPaths;

    public function __construct(array $claim, array $attachmentPaths = [])
    {
        $this->claim = $claim;
        $this->attachmentPaths = $attachmentPaths;
    }

    public function attachments(): array
    {
        return collect($this->attachmentPaths)
            ->filter(fn($path) => file_exists($path))
            ->map(fn($path) => Attachment::fromPath($path))
            ->values()
            ->all();
    }

    public function envelope(): Envelope
    {
        // No internal claim number in the subject — like sticker requests,
        // this reference never leaves our system, so it can't help the
        // underwriter identify anything. They reference the thread by the
        // vehicle/customer details in the body instead.
        return new Envelope(
            subject: 'New Claim Logged - ' . ($this->claim['registration_number'] ?? $this->claim['customer_name'] ?? ''),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.claim-logged',
            with: ['claim' => $this->claim],
        );
    }
}
