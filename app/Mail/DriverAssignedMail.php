<?php

namespace App\Mail;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DriverAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $shipment;

    public function __construct(Shipment $shipment)
    {
        $this->shipment = $shipment;
    }

    public function build()
    {
        return $this->subject('🚚 Tugas Pengiriman Baru CoCoLink - Shipment #' . $this->shipment->id)
                    ->view('emails.driver_assigned');
    }
}
