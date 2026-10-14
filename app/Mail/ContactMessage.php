<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nome,
        public string $email,
        public string $mensagem,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nova mensagem do site: '.$this->nome,
            replyTo: [new Address($this->email, $this->nome)],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.contact-message');
    }
}
