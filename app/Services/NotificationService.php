<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public static function saveNotification($tenant_id, $sender, $destinatary, $type, $text, $service_day, $service_start_hour, $service_end_hour)
    {
        return Notification::create([
            'tenant_id' => $tenant_id,
            'sender' => $sender,
            'destinatary' => $destinatary,
            'type' => $type,
            'text' => $text,
            'service_day' => $service_day,
            'service_start_hour' => $service_start_hour,
            'service_end_hour' => $service_end_hour,
            'status' => 'scheduled',
        ]);
    }

    public static function markAsSent($notificationId): bool
    {
        $notification = Notification::find($notificationId);

        if ($notification) {
            $notification->status = 'sent';
            $notification->save();
            return true;
        }
        return false;
    }
}
