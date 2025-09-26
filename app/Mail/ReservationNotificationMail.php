<?php

namespace App\Mail;

use App\Models\Reservations;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $type;
    /**
     * Create a new message instance.
     */
    public function __construct(Reservations $reservations,string $type)
    {
        $this->reservation = $reservations;
        $this->type = $type;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->type) {
            'pending' => 'Reservasi Baru Menunggu Persetujuan',
            'Approved' => 'Reservasi telah Disetujui',
            'Rejected' => 'Reservasi Ditolak',
            default => 'informasi Reservasi'
        };
        return new Envelope(
            subject: 'Reservatio Notification Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.reservation_notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
