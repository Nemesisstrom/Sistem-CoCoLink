<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_number',
        'order_allocation_id',
        'driver_id',
        'pickup_time',
        'delivered_time',
        'proof_of_delivery',
        'status',
    ];

    protected $casts = [
        'pickup_time' => 'datetime',
        'delivered_time' => 'datetime',
    ];

    // Shipment -> Merujuk ke 1 Alokasi Pesanan
    public function allocation()
    {
        return $this->belongsTo(OrderAllocation::class, 'order_allocation_id');
    }

    // Shipment -> Ditangani oleh 1 Driver Logistik
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
