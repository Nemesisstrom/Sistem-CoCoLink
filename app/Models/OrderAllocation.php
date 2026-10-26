<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'koperasi_anak_id',
        'allocated_quantity',
        'status',
    ];

    // Alokasi -> Merujuk ke Order Utama
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // Alokasi -> Ditugaskan ke 1 Koperasi Anak
    public function koperasiAnak()
    {
        return $this->belongsTo(User::class, 'koperasi_anak_id');
    }

    // Alokasi -> Memiliki 1 Tugas Pengiriman (Shipment)
    public function shipment()
    {
        return $this->hasOne(Shipment::class, 'order_allocation_id');
    }
}
