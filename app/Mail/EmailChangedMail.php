<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $previousEmail) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Email Address Has Been Changed',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.email-changed',
            with: [
                'user' => $this->user,
                'previousEmail' => $this->previousEmail,
            ],
        );
    }
}
