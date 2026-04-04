<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array  $data  Datos del formulario validados
     * @param string $type  'internal' | 'confirmation'
     */
    public function __construct(
        public array $data,
        public string $type = 'internal'
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->type === 'internal'
            ? 'Nuevo contacto desde ArrowK — ' . $this->data['name']
            : 'Recibimos tu mensaje — ArrowK';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $view = $this->type === 'internal'
            ? 'emails.contact-internal'
            : 'emails.contact-confirmation';

        return new Content(view: $view);
    }
}