<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public static function saveNotification($sender, $destinatary, $type, $text)
    {
        return Notification::create([
            'tenant_id' => 1,
            'sender' => $sender,
            'destinatary' => $destinatary,
            'type' => $type,
            'text' => $text,
            'status' => 'scheduled',
        ]);
    }
}
