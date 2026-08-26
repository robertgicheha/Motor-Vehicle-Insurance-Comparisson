<?php

namespace App\Mail;

use App\Mail\Concerns\UsesEmailTemplate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendingEmailVerification extends Mailable
{
    use Queueable, SerializesModels, UsesEmailTemplate;

    public function __construct(
        protected User $user,
        public string $verificationUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->envelopeSubject($this->template('pending_email_verification'), 'Verify Your New Email Address', $this->variables()),
        );
    }

    public function content(): Content
    {
        $template = $this->template('pending_email_verification');

        if ($template) {
            return $this->dynamicContent($template, $this->variables());
        }

        return new Content(
            markdown: 'emails.pending-email-verification',
            with: [
                'user' => $this->user,
                'verificationUrl' => $this->verificationUrl,
            ],
        );
    }

    protected function variables(): array
    {
        return [
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'verify_url' => $this->verificationUrl,
            'app_name' => config('app.name'),
        ];
    }
}
