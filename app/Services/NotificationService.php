<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function saveNotification($sender, $destinatary, $type, $text)
    {
        return Notification::create([
            'sender' => $sender,
            'destinatary' => $destinatary,
            'type' => $type,
            'text' => $text,
            'status' => 'scheduled',
        ]);
    }
}
