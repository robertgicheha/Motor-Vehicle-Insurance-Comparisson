<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserEmailChanged extends Notification
{
    use Queueable;

    public function __construct(protected User $targetUser, protected string $previousEmail) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Email Address Changed — {$this->targetUser->name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("{$this->targetUser->name} changed their email address from {$this->previousEmail} to {$this->targetUser->email} on " . now()->format('d M Y, h:i A') . '.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->targetUser->id,
            'user_name' => $this->targetUser->name,
            'previous_email' => $this->previousEmail,
            'new_email' => $this->targetUser->email,
            'message' => "{$this->targetUser->name} changed their email from {$this->previousEmail} to {$this->targetUser->email}",
        ];
    }
}
