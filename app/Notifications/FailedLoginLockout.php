<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FailedLoginLockout extends Notification
{
    use Queueable;

    public function __construct(protected string $email, protected string $ip) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'email' => $this->email,
            'ip' => $this->ip,
            'message' => "Repeated failed login attempts for '{$this->email}' from IP {$this->ip} — account temporarily locked out.",
        ];
    }
}
