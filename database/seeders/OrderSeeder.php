<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Models\WasteType;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $ptX = User::where('email', 'ptx@gmail.com')->first();
        $arangBatok = WasteType::where('name', 'Arang Batok Kelapa')->first();
        $sabutKelapa = WasteType::where('name', 'Sabut Kelapa (Coco Fiber)')->first();

        if ($ptX && $arangBatok) {
            Order::firstOrCreate(
                ['order_number' => 'ORD-20261023-001'],
                [
                    'pengepul_id' => $ptX->id,
                    'waste_type_id' => $arangBatok->id,
                    'quantity' => 50.00,
                    'price_per_unit' => 3500000.00,
                    'total_amount' => 175000000.00,
                    'delivery_address' => $ptX->address,
                    'status' => 'allocated',
                ]
            );
        }

        if ($ptX && $sabutKelapa) {
            Order::firstOrCreate(
                ['order_number' => 'ORD-20261023-002'],
                [
                    'pengepul_id' => $ptX->id,
                    'waste_type_id' => $sabutKelapa->id,
                    'quantity' => 30.00,
                    'price_per_unit' => 2000000.00,
                    'total_amount' => 60000000.00,
                    'delivery_address' => $ptX->address,
                    'status' => 'pending',
                ]
            );
        }
    }
}
