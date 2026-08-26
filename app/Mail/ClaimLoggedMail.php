<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaimLoggedMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $claim;

    public function __construct(array $claim)
    {
        $this->claim = $claim;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Claim Logged - ' . $this->claim['claim_number'],
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
