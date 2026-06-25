<?php

namespace App\Notifications;

use App\Models\ChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ChangeRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected ChangeRequest $changeRequest,
        protected string $status,
        protected ?string $notes = null,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $action = $this->changeRequest->action;
        $type = $this->changeRequest->type;

        if ($this->status === 'approved') {
            $message = "Your {$type} {$action} request #{$this->changeRequest->id} has been approved.";
        } else {
            $message = "Your {$type} {$action} request #{$this->changeRequest->id} has been declined.";
        }

        if ($this->notes) {
            $message .= " Review notes: {$this->notes}";
        }

        return [
            'action' => "change_request_{$this->status}",
            'message' => $message,
            'change_request_id' => $this->changeRequest->id,
            'type' => $this->changeRequest->type,
            'status' => $this->status,
        ];
    }
}
