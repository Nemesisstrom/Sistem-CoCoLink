<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'koperasi_anak_id',
        'waste_type_id',
        'stock_in',
        'stock_out',
        'current_stock',
        'entry_date',
        'notes',
    ];

    public function koperasiAnak()
    {
        return $this->belongsTo(User::class, 'koperasi_anak_id');
    }

    public function wasteType()
    {
        return $this->belongsTo(WasteType::class, 'waste_type_id');
    }
}
