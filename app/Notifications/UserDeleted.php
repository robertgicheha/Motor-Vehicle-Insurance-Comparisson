<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserDeleted extends Notification
{
    use Queueable;

    public function __construct(protected User $deletedUser, protected User $deletedBy) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'user_deleted',
            'title' => 'User deleted',
            'message' => "{$this->deletedBy->username} deleted user {$this->deletedUser->username}.",
            'icon' => 'trash',
            'color' => '#dc2626',
            'link' => route('admin.users.trash'),
            'user_id' => $this->deletedUser->id,
        ];
    }
}
