<?php

namespace App\Notifications;

use App\Models\Agenda;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class BookingConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    public function __construct(Agenda $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $booking = $this->booking->loadMissing('client', 'service', 'professional');

        $clientName = $booking->client->name ?? 'Cliente';
        $date = Carbon::parse($booking->day)->format('d/m/Y');
        $time = $booking->start_hour;
        $service = $booking->service->name;
        $professional = optional($booking->professional)->name ?? 'N/A';

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Confirmação da Sua Marcação')
            ->view('emails.booking.confirmation', [
                'booking' => $booking,
                'date' => Carbon::parse($booking->day)->format('d/m/Y'),
                'time' => $booking->start_hour,
                'service' => $booking->service->name,
                'professional' => optional($booking->professional)->name ?? 'N/A',
                'clientName' => $clientName,
            ])
            ->bcc('heliojsmoreira@gmail.com');
    }
}
