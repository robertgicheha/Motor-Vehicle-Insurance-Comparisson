<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserCredentialsAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $password, public User $admin) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "A User Account Has Been Created — {$this->user->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user-credentials-admin',
            with: [
                'user' => $this->user,
                'password' => $this->password,
                'admin' => $this->admin,
            ],
        );
    }
}
