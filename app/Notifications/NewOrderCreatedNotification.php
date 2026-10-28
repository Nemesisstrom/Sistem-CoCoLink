<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewOrderCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        // Mengecek relasi secara terpisah menggunakan string
        if (! $order->relationLoaded('pengepul') || ! $order->relationLoaded('wasteType')) {
            $order->load(['pengepul', 'wasteType']);
        }

        $this->order = $order;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $pengepulName = $this->order->pengepul?->name ?? 'Pelanggan';
        $wasteTypeName = $this->order->wasteType?->name ?? 'Limbah';

        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'pengepul_name' => $pengepulName,
            'waste_type' => $wasteTypeName,
            'quantity' => $this->order->quantity,
            'message' => "Pesanan baru {$this->order->order_number} masuk dari {$pengepulName}.",
        ];
    }
}
