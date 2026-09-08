<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserVerified extends Notification
{
    use Queueable;

    public function __construct(protected User $verifiedUser, protected User $verifiedBy) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("User Verified — {$this->verifiedUser->name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("{$this->verifiedUser->name} ({$this->verifiedUser->email}) has been verified and can now log in.")
            ->line("Verified by: {$this->verifiedBy->name}")
            ->action('View Users', route('admin.users.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->verifiedUser->id,
            'user_name' => $this->verifiedUser->name,
            'user_email' => $this->verifiedUser->email,
            'verified_by' => $this->verifiedBy->name,
            'message' => "{$this->verifiedUser->name} ({$this->verifiedUser->email}) was verified by {$this->verifiedBy->name}",
        ];
    }
}
