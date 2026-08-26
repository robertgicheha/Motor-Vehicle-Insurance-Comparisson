<?php

namespace App\Mail;

use App\Mail\Concerns\UsesEmailTemplate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserCredentialsMail extends Mailable
{
    use Queueable, SerializesModels, UsesEmailTemplate;

    public function __construct(protected User $user, protected string $password) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->envelopeSubject($this->template('user_credentials'), 'Your Account Credentials - Motor Vehicle Insurance Portal', $this->variables()),
        );
    }

    public function content(): Content
    {
        $template = $this->template('user_credentials');

        if ($template) {
            return $this->dynamicContent($template, $this->variables());
        }

        return new Content(
            markdown: 'emails.user-credentials',
            with: [
                'user' => $this->user,
                'password' => $this->password,
            ],
        );
    }

    protected function variables(): array
    {
        return [
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'password' => $this->password,
            'login_url' => route('login'),
            'app_name' => config('app.name'),
        ];
    }
}
