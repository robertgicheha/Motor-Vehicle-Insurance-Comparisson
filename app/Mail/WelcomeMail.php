<?php

namespace App\Mail;

use App\Mail\Concerns\UsesEmailTemplate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels, UsesEmailTemplate;

    public function __construct(protected User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->envelopeSubject($this->template('welcome'), 'Welcome to Motor Vehicle Insurance Portal', $this->variables()),
        );
    }

    public function content(): Content
    {
        $template = $this->template('welcome');

        if ($template) {
            return $this->dynamicContent($template, $this->variables());
        }

        return new Content(
            markdown: 'emails.welcome',
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
            'profile_url' => route('profile.edit'),
            'app_name' => config('app.name'),
        ];
    }
}
