<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendingEmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected User $user,
        public string $verificationUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Your New Email Address',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.pending-email-verification',
            with: [
                'user' => $this->user,
                'verificationUrl' => $this->verificationUrl,
            ],
        );
    }
}
