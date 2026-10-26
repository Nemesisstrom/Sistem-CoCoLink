<?php

namespace Database\Seeders;

use App\Models\WasteType;
use Illuminate\Database\Seeder;

class WasteTypeSeeder extends Seeder
{
    public function run(): void
    {
        WasteType::firstOrCreate(
            ['name' => 'Arang Batok Kelapa'],
            ['unit' => 'Ton', 'description' => 'Arang batok kelapa mentah olahan kualitas ekspor.']
        );

        WasteType::firstOrCreate(
            ['name' => 'Sabut Kelapa (Coco Fiber)'],
            ['unit' => 'Ton', 'description' => 'Serat sabut kelapa yang siap diolah menjadi matras/jok.']
        );

        WasteType::firstOrCreate(
            ['name' => 'Air Kelapa'],
            ['unit' => 'Liter', 'description' => 'Air kelapa murni sisa pengolahan untuk bahan kecap/nata de coco.']
        );

        WasteType::firstOrCreate(
            ['name' => 'Minyak Kelapa Murni (VCO)'],
            ['unit' => 'Liter', 'description' => 'Minyak kelapa murni hasil ekstraksi limbah daging kelapa.']
        );
    }
}
