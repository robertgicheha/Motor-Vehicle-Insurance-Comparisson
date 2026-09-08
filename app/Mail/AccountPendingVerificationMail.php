<?php

namespace App\Mail;

use App\Mail\Concerns\UsesEmailTemplate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountPendingVerificationMail extends Mailable
{
    use Queueable, SerializesModels, UsesEmailTemplate;

    public function __construct(protected User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->envelopeSubject($this->template('account_pending_verification'), 'Your Account Has Been Created — Pending Verification', $this->variables()),
        );
    }

    public function content(): Content
    {
        $template = $this->template('account_pending_verification');

        if ($template) {
            return $this->dynamicContent($template, $this->variables());
        }

        return new Content(
            markdown: 'emails.account-pending-verification',
            with: ['user' => $this->user],
        );
    }

    protected function variables(): array
    {
        return [
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'app_name' => config('app.name'),
        ];
    }
}
