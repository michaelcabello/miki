<?php

namespace App\Mail\Orders;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    // Definimos las propiedades públicas para que la vista las vea
    public function __construct(
        public PurchaseOrder $order,
        public string $downloadUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Solicitud de Cotización: {$this->order->name} - TICOM",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'admin.emails.purchase-order', // Ruta de la vista
        );
    }

    public function attachments(): array
    {
        return []; // Sin adjuntos para evitar bloqueos de red
    }
}
