<?php

namespace App\Mail;

use App\Models\BetaSignup;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Aviso interno: nova inscrição na beta. */
class BetaSignupReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public BetaSignup $signup) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nova inscrição na beta: '.$this->signup->nome,
            replyTo: [new Address($this->signup->email, $this->signup->nome)],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.beta-signup-received',
            with: ['total' => BetaSignup::count()],
        );
    }
}
