<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PembayaranMail extends Mailable
{
    use Queueable, SerializesModels;

    private $pembayaran;
    private $to_custom;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pembayaran, $to_custom)
    {
        $this->pembayaran = $pembayaran;
        $this->to_custom = $to_custom;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Pembayaran',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'pembayaran.mail',
            with: [
                'pembayaran' => $this->pembayaran,
                'to_custom' => $this->to_custom
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
