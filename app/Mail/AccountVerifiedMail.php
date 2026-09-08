<?php

namespace App\Mail;

use App\Mail\Concerns\UsesEmailTemplate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountVerifiedMail extends Mailable
{
    use Queueable, SerializesModels, UsesEmailTemplate;

    public function __construct(protected User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->envelopeSubject($this->template('account_verified'), 'Your Account Has Been Verified', $this->variables()),
        );
    }

    public function content(): Content
    {
        $template = $this->template('account_verified');

        if ($template) {
            return $this->dynamicContent($template, $this->variables());
        }

        return new Content(
            markdown: 'emails.account-verified',
            with: ['user' => $this->user],
        );
    }

    protected function variables(): array
    {
        return [
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'login_url' => route('login'),
            'dashboard_url' => route('dashboard'),
            'app_name' => config('app.name'),
        ];
    }
}
