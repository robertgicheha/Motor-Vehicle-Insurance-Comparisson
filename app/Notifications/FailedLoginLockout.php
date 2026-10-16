<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FailedLoginLockout extends Notification
{
    use Queueable;

    public function __construct(protected string $username, protected string $ip) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'username' => $this->username,
            'ip' => $this->ip,
            'message' => "Repeated failed login attempts for '{$this->username}' from IP {$this->ip} — account temporarily locked out.",
        ];
    }
}
