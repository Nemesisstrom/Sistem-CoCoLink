<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'pengepul_id',
        'waste_type_id',
        'quantity',
        'price_per_unit',
        'total_amount',
        'delivery_address',
        'status',
    ];

    // Order -> Dimiliki oleh 1 Pengepul / Pembeli Industri
    public function pengepul()
    {
        return $this->belongsTo(User::class, 'pengepul_id');
    }

    // Order -> Menggunakan 1 Jenis Limbah
    public function wasteType()
    {
        return $this->belongsTo(WasteType::class, 'waste_type_id');
    }

    // Order -> Memiliki banyak Alokasi ke Koperasi Anak
    public function allocations()
    {
        return $this->hasMany(OrderAllocation::class, 'order_id');
    }

    // Order -> Memiliki banyak Pengiriman melalui Alokasi (Has Many Through)
    public function shipments()
    {
        return $this->hasManyThrough(Shipment::class, OrderAllocation::class, 'order_id', 'order_allocation_id');
    }
}
