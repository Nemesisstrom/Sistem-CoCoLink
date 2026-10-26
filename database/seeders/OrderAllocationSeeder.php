<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderAllocation;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderAllocationSeeder extends Seeder
{
    public function run(): void
    {
        $orderArang = Order::where('order_number', 'ORD-20261023-001')->first();
        $koperasiA1 = User::where('email', 'koperasiA1@cocolink.id')->first();
        $koperasiA2 = User::where('email', 'koperasiA2@cocolink.id')->first();

        if ($orderArang && $koperasiA1 && $koperasiA2) {
            OrderAllocation::firstOrCreate(
                ['order_id' => $orderArang->id, 'koperasi_anak_id' => $koperasiA1->id],
                [
                    'allocated_quantity' => 30.00,
                    'status' => 'ready',
                ]
            );

            OrderAllocation::firstOrCreate(
                ['order_id' => $orderArang->id, 'koperasi_anak_id' => $koperasiA2->id],
                [
                    'allocated_quantity' => 20.00,
                    'status' => 'assigned',
                ]
            );
        }
    }
}
