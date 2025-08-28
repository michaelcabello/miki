<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PasswordChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $newPassword;

    public function __construct(User $user, $newPassword)
    {
        $this->user = $user;
         $this->newPassword = $newPassword;
    }



    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu contraseña ha sido actualizada',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'admin.emails.password-changed',
        );
    }


    public function attachments(): array
    {
        return [];
    }
}
