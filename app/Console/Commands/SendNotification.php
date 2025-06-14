<?php

namespace App\Console\Commands;

use App\Models\Notification;
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $notifications = Notification::where('status', 'scheduled')
                ->where('service_day', Carbon::tomorrow()->format('Y-m-d'))
                ->get();

            foreach ($notifications as $notification) {
                SmsService::send(
                    $notification->sender,
                    $notification->destinatary,
                    $notification->text,
                );
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Error sending notifications: ' . $e->getMessage());
            return false;
        }
    }
}
