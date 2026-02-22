<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PortalAccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $resetUrl;
    public string $partnerName;


    public function __construct(string $partnerName, string $resetUrl)
    {
        $this->partnerName = $partnerName;
        $this->resetUrl = $resetUrl;
    }


    /* public function build()
    {
        return $this->subject('Acceso habilitado al Portal de Clientes')
            ->view('admin.emails.portal-access');
    } */

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Acceso habilitado al Portal de Clientes',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
         return new Content(
            view: 'admin.emails.portal-access',
        );
    }


    public function attachments(): array
    {
        return [];
    }
}
