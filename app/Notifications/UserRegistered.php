<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserRegistered extends Notification
{
    use Queueable;

    public function __construct(protected User $registeredUser) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->registeredUser->id,
            'user_name' => $this->registeredUser->name,
            'user_email' => $this->registeredUser->email,
            'message' => "New user registered: {$this->registeredUser->name} ({$this->registeredUser->email})",
        ];
    }
}
