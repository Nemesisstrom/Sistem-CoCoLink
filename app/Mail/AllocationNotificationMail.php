<?php

namespace App\Mail;

use App\Models\OrderAllocation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AllocationNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $allocation;

    public function __construct(OrderAllocation $allocation)
    {
        $this->allocation = $allocation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notifikasi Kuota Pesanan Baru - CoCoLink (' . $this->allocation->order->order_number . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.allocation_notification',
        );
    }
}
