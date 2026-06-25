<?php

namespace App\Notifications;

use App\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EnquiryResponse extends Notification
{
    use Queueable;

    public function __construct(
        public Enquiry $enquiry,
        public string $response
    ) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'enquiry_id' => $this->enquiry->id,
            'subject' => $this->enquiry->subject,
            'response' => $this->response,
            'message' => "Your enquiry \"{$this->enquiry->subject}\" has been responded to.",
        ];
    }
}
