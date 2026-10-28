<?php

namespace App\Mail;

use App\Models\OrderAllocation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AllocationNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $allocation;

    public function __construct(OrderAllocation $allocation)
    {
        // Pastikan relasi order dimuat secara eksplisit untuk mencegah N+1 query pada queue
        $this->allocation = $allocation->relationLoaded('order')
            ? $allocation
            : $allocation->load('order');
    }

    public function envelope(): Envelope
    {
        $orderNumber = $this->allocation->order?->order_number ?? 'N/A';

        return new Envelope(
            subject: 'Notifikasi Kuota Pesanan Baru - CoCoLink (' . $orderNumber . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.allocation_notification',
        );
    }
}
