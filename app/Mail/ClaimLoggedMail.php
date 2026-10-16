<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
        // Only documents that survived the ESET scan at upload and are still
        // present on disk are attached; anything missing is skipped (with a
        // warning) so the underwriter email never fails over a bad path.
        return collect($this->attachmentPaths)
            ->filter(function ($path) {
                if (! file_exists($path) || filesize($path) === 0) {
                    Log::warning('Claim email attachment skipped (missing or empty): ' . $path);

                    return false;
                }

                return true;
            })
            ->map(fn($path) => Attachment::fromPath($path))
            ->values()
            ->all();
    }

    public function envelope(): Envelope
    {
        // The subject references the vehicle registration only — no personal
        // customer data (and the internal claim number is just a fallback,
        // since the vehicle is the identity the underwriter knows the case by).
        return new Envelope(
            subject: 'New Claim Logged - ' . ($this->claim['registration_number'] ?? ($this->claim['claim_number'] ?? '')),
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
