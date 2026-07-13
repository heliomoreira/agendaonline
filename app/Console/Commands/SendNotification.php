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

            $now = Carbon::now();
            $today = $now->format('Y-m-d');
            $timeFormatted = $now->format('H:i:00');

            $notifications = Notification::where('status', 'scheduled')
                // já devia ter saído (data de envio passou, ou é hoje e a hora chegou)
                ->where(function ($q) use ($today, $timeFormatted) {
                    $q->where('send_day', '<', $today)
                        ->orWhere(function ($q2) use ($today, $timeFormatted) {
                            $q2->where('send_day', $today)
                                ->where('send_hour', '<=', $timeFormatted);
                        });
                })
                // mas o serviço ainda não passou (senão o lembrete é inútil)
                ->where(function ($q) use ($today, $timeFormatted) {
                    $q->where('service_day', '>', $today)
                        ->orWhere(function ($q2) use ($today, $timeFormatted) {
                            $q2->where('service_day', $today)
                                ->where('service_start_hour', '>=', $timeFormatted);
                        });
                })
                ->get();

            foreach ($notifications as $notification) {
                SmsService::send(
                    $notification->destinatary,
                    $notification->text,
                    $notification->sender,
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
