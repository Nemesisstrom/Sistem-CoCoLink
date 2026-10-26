<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'parent_koperasi_id',
        'phone_number',
        'address',
        'vehicle_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Koperasi Induk -> Memiliki banyak Koperasi Anak
    public function childCooperatives()
    {
        return $this->hasMany(User::class, 'parent_koperasi_id');
    }

    // Koperasi Anak -> Merujuk ke Koperasi Induk
    public function parentCooperative()
    {
        return $this->belongsTo(User::class, 'parent_koperasi_id');
    }

    // Pengepul / Pembeli -> Memiliki banyak Order
    public function orders()
    {
        return $this->hasMany(Order::class, 'pengepul_id');
    }

    // Koperasi Anak -> Memiliki banyak Alokasi Order
    public function allocations()
    {
        return $this->hasMany(OrderAllocation::class, 'koperasi_anak_id');
    }

    // Driver -> Memiliki banyak Tugas Pengiriman (Shipments)
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'driver_id');
    }
}
