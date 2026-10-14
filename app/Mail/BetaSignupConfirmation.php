<?php

namespace App\Mail;

use App\Models\BetaSignup;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Confirmação enviada a quem se inscreve na beta. */
class BetaSignupConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public BetaSignup $signup) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'A sua inscrição na beta da Agenda Online');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.beta-signup-confirmation');
    }
}
