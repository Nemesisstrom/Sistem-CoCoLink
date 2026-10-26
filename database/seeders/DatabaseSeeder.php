<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            WasteTypeSeeder::class,
            OrderSeeder::class,
            OrderAllocationSeeder::class,
            ShipmentSeeder::class,
        ]);
    }
}
