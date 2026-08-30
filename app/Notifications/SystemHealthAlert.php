<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemHealthAlert extends Notification
{
    use Queueable;

    public function __construct(protected string $type, protected string $message) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'message' => "System health check failed ({$this->type}): {$this->message}",
        ];
    }
}
