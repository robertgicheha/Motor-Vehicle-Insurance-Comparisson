<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserPasswordChanged extends Notification
{
    use Queueable;

    public function __construct(protected User $targetUser) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Password Changed — {$this->targetUser->name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("{$this->targetUser->name} ({$this->targetUser->email}) changed their password on " . now()->format('d M Y, h:i A') . '.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->targetUser->id,
            'user_name' => $this->targetUser->name,
            'user_email' => $this->targetUser->email,
            'message' => "{$this->targetUser->name} ({$this->targetUser->email}) changed their password",
        ];
    }
}
