<?php

namespace App\Notifications;

use App\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewEnquiry extends Notification
{
    use Queueable;

    public function __construct(public Enquiry $enquiry) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'enquiry_id' => $this->enquiry->id,
            'subject' => $this->enquiry->subject,
            'message' => "New enquiry received: \"{$this->enquiry->subject}\"",
        ];
    }
}
