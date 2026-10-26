<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'description',
    ];

    // Jenis Limbah -> Digunakan pada banyak Order
    public function orders()
    {
        return $this->hasMany(Order::class, 'waste_type_id');
    }
}
