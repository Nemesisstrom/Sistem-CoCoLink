<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderCreatedNotification extends Notification
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable): array
    {
        return ['database']; // Menyimpan notifikasi ke basis data
    }

    public function toArray($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'pengepul_name' => $this->order->pengepul->name,
            'waste_type' => $this->order->wasteType->name,
            'quantity' => $this->order->quantity,
            'message' => "Pesanan baru {$this->order->order_number} masuk dari {$this->order->pengepul->name}.",
        ];
    }
}
