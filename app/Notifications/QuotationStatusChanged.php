<?php

namespace App\Notifications;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuotationStatusChanged extends Notification
{
    use Queueable;

    public Quotation $quotation;
    public string $oldStatus;
    public string $newStatus;

    public function __construct(Quotation $quotation, string $oldStatus, string $newStatus)
    {
        $this->quotation = $quotation;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'quotation_id' => $this->quotation->id,
            'quotation_number' => $this->quotation->quotation_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => "Quotation {$this->quotation->quotation_number} status changed from {$this->oldStatus} to {$this->newStatus}",
        ];
    }
}
