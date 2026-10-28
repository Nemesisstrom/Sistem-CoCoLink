<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'price_per_unit',
        'unit',
        'icon',
        'description',
    ];

    // Relasi Jenis Limbah ke Order
    public function orders()
    {
        return $this->hasMany(Order::class, 'waste_type_id');
    }

    // Helper format nama satuan dalam Bahasa Indonesia
    public function getFormattedUnitAttribute(): string
    {
        return match ($this->unit) {
            'ton'   => 'Ton (1.000 kg)',
            'kg'    => 'Kilogram (kg)',
            'liter' => 'Liter (L)',
            'pcs'   => 'Pcs / Karung',
            default => strtoupper($this->unit),
        };
    }
}
