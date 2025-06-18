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
        $booking = $this->booking;

        $date = \Carbon\Carbon::parse($booking->day)->format('d/m/Y');
        $time = $booking->start_hour;
        $service = $booking->service->name;
        $professional = optional($booking->professional)->name ?? 'N/A';

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Confirmação da Sua Marcação')
            ->greeting("Olá {$booking->client->name},")
            ->markdown('emails.booking.confirmation', compact('booking', 'date', 'time', 'service', 'professional'))
            ->bcc('heliojsmoreira@gmail.com');
    }
}
