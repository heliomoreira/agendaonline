<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Services\NotificationService;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS notifications scheduled for the next day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

            $currentTime = Carbon::now();
            $timeFormatted = $currentTime->format('H:i:00');

            $notifications = Notification::where('status', 'scheduled')
                ->where('service_day', Carbon::tomorrow()->format('Y-m-d'))
                ->where(function($query) use ($timeFormatted) {
                    $query->where('send_hour', '=', $timeFormatted);
                })
                ->get();

            foreach ($notifications as $notification) {
                SmsService::send(
                    $notification->tenant_id,
                    $notification->sender,
                    $notification->destinatary,
                    $notification->text,
                );

                NotificationService::markAsSent($notification->id);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Error sending notifications: ' . $e->getMessage());
            return false;
        }
    }
}
