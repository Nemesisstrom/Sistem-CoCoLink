<?php

namespace Database\Seeders;

use App\Models\OrderAllocation;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    public function run(): void
    {
        $allocationA1 = OrderAllocation::first();
        $driver = User::where('email', 'driver1@cocolink.id')->first();

        if ($allocationA1 && $driver) {
            Shipment::firstOrCreate(
                ['shipment_number' => 'TRK-20261023-001'],
                [
                    'order_allocation_id' => $allocationA1->id,
                    'driver_id' => $driver->id,
                    'pickup_time' => now(),
                    'delivered_time' => null,
                    'proof_of_delivery' => null,
                    'status' => 'picking_up',
                ]
            );
        }
    }
}
