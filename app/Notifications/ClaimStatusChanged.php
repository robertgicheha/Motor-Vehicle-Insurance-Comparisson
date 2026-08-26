<?php

namespace App\Notifications;

use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ClaimStatusChanged extends Notification
{
    use Queueable;

    public Claim $claim;
    public string $oldStatus;
    public string $newStatus;

    public function __construct(Claim $claim, string $oldStatus, string $newStatus)
    {
        $this->claim = $claim;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $statusLabel = Claim::statuses()[$this->newStatus] ?? $this->newStatus;

        return [
            'claim_id' => $this->claim->id,
            'claim_number' => $this->claim->claim_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => "Claim {$this->claim->claim_number} status changed to {$statusLabel}",
        ];
    }
}
