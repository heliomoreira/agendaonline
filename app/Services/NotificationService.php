<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NotificationService
{
    public function getFiltered(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = Notification::query();

        if (isset($filters['tenant_id'])) {
            $query->where('tenant_id', $filters['tenant_id']);
        }

        if (isset($filters['recipient_type'])) {
            $query->where('recipient_type', $filters['recipient_type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('service_day', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('service_day', '<=', $filters['date_to']);
        }

        return $query->orderBy('send_day')->paginate($perPage)->withQueryString();
    }
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

    public static function markAsFailed($notificationId, ?string $reason = null): bool
    {
        $notification = Notification::find($notificationId);

        if ($notification) {
            $notification->status = 'failed';

            if ($reason) {
                Log::warning('SMS falhado', ['notification' => $notificationId, 'reason' => $reason]);
            }

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
