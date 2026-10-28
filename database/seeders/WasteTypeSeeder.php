<?php

namespace Database\Seeders;

use App\Models\WasteType;
use Illuminate\Database\Seeder;

class WasteTypeSeeder extends Seeder
{
    public function run(): void
    {
        $wasteTypes = [
            [
                'code'           => 'TON-ARNG',
                'name'           => 'Arang Batok Kelapa',
                'price_per_unit' => 8500000,
                'unit'           => 'ton',
                'icon'           => 'bi-fire',
                'description'    => 'Arang batok kelapa mentah olahan kualitas ekspor.',
            ],
            [
                'code'           => 'TON-SABUT',
                'name'           => 'Sabut Kelapa (Coir Fibre)',
                'price_per_unit' => 3500000,
                'unit'           => 'ton',
                'icon'           => 'bi-box-seam',
                'description'    => 'Serat sabut kelapa pres untuk industri jok dan tekstil.',
            ],
            [
                'code'           => 'LTR-AIR',
                'name'           => 'Air Kelapa Murni',
                'price_per_unit' => 1500,
                'unit'           => 'liter',
                'icon'           => 'bi-droplet-fill',
                'description'    => 'Air kelapa murni untuk bahan baku nata de coco atau minuman.',
            ],
            [
                'code'           => 'KG-COCOPEAT',
                'name'           => 'Cocopeat / Serbuk Sabut',
                'price_per_unit' => 2500,
                'unit'           => 'kg',
                'icon'           => 'bi-flower1',
                'description'    => 'Media tanam organik hasil olahan sabut kelapa.',
            ],
        ];

        foreach ($wasteTypes as $type) {
            WasteType::updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}
