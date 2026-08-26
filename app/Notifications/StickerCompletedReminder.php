<?php

namespace App\Notifications;

use App\Models\Sticker;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StickerCompletedReminder extends Notification
{
    use Queueable;

    public Sticker $sticker;

    public function __construct(Sticker $sticker)
    {
        $this->sticker = $sticker;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'sticker_id' => $this->sticker->id,
            'sticker_number' => $this->sticker->sticker_number,
            'company' => $this->sticker->company?->name ?? 'Unknown',
            'message' => "Certificate {$this->sticker->sticker_number} has been issued to {$this->sticker->company?->name}. Please go to Update Stickers to mark it as completed once the certificate PDF has been generated.",
            'action_url' => route('admin.stickers.update-form'),
            'icon' => 'bell',
        ];
    }
}
