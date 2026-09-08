<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRegistered extends Notification
{
    use Queueable;

    public function __construct(protected User $registeredUser, protected bool $pendingVerification = false) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->pendingVerification
                ? "New Signup Pending Verification — {$this->registeredUser->name}"
                : "New User Registered — {$this->registeredUser->name}")
            ->greeting("Hello {$notifiable->name},");

        if ($this->pendingVerification) {
            $mail->line("A new user has self-registered and is awaiting your verification before they can log in.")
                ->line("Name: {$this->registeredUser->name}")
                ->line("Email: {$this->registeredUser->email}")
                ->action('Review Pending Users', route('admin.pending-users.index'))
                ->line('Please confirm the account is genuine before activating it.');
        } else {
            $mail->line("A new user has registered on the portal.")
                ->line("Name: {$this->registeredUser->name}")
                ->line("Email: {$this->registeredUser->email}")
                ->action('View Users', route('admin.users.index'));
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->registeredUser->id,
            'user_name' => $this->registeredUser->name,
            'user_email' => $this->registeredUser->email,
            'pending_verification' => $this->pendingVerification,
            'message' => $this->pendingVerification
                ? "New user pending verification: {$this->registeredUser->name} ({$this->registeredUser->email})"
                : "New user registered: {$this->registeredUser->name} ({$this->registeredUser->email})",
        ];
    }
}
