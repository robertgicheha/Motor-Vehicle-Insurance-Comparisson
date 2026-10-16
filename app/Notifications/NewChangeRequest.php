<?php

namespace App\Notifications;

use App\Models\ChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewChangeRequest extends Notification
{
    use Queueable;

    public function __construct(protected ChangeRequest $changeRequest) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $adminUsername = $this->changeRequest->user->username;

        return [
            'action' => 'new_change_request',
            'message' => "{$adminUsername} requested to {$this->changeRequest->action} {$this->changeRequest->type}.",
            'change_request_id' => $this->changeRequest->id,
            'type' => $this->changeRequest->type,
            'status' => 'pending',
        ];
    }
}
