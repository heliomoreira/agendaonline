<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Setting;
use Carbon\Carbon;

class NotificationService
{
    public static function saveNotification($tenant_id, $appointment_id, $sender, $destinatary, $type, $text, $service_day, $service_start_hour, $service_end_hour, $recipient_type = 'client')
    {
        $advanceDays = (int) (Setting::current()->sms_advance_days ?? 1);
        $sendDate = Carbon::parse($service_day)->subDays($advanceDays);

        if ($sendDate->isPast()) {
            $sendDate = Carbon::today();
        }

        return Notification::create([
            'tenant_id' => $tenant_id,
            'appointment_id' => $appointment_id,
            'sender' => $sender,
            'destinatary' => $destinatary,
            'recipient_type' => $recipient_type,
            'type' => $type,
            'text' => $text,
            'service_day' => $service_day,
            'service_start_hour' => $service_start_hour,
            'service_end_hour' => $service_end_hour,
            'send_day' => $sendDate->toDateString(),
            'send_hour' => Setting::current()->sms_send_hour ?? "18:00:00",
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

    public function deleteNotification($tenant_id, $appointment_id)
    {
        return Notification::where('tenant_id', $tenant_id)
            ->where('appointment_id', $appointment_id)
            ->delete();
    }
}
