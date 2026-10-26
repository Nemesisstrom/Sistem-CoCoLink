<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\WasteType;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Induk Koperasi
        $induk = User::create([
            'name' => 'Koperasi Induk Utama (Central Ops)',
            'email' => 'induk@cocolink.id',
            'password' => Hash::make('password'),
            'role' => 'koperasi_induk',
            'phone_number' => '081234567890',
            'address' => 'Kantor Pusat CoCoLink'
        ]);

        // 2. Koperasi Anak
        $a1 = User::create([
            'name' => 'Koperasi Anak A1',
            'email' => 'koperasiA1@cocolink.id',
            'password' => Hash::make('password'),
            'role' => 'koperasi_anak',
            'parent_koperasi_id' => $induk->id,
            'phone_number' => '081234567891',
            'address' => 'Wilayah A1'
        ]);

        $a2 = User::create([
            'name' => 'Koperasi Anak A2',
            'email' => 'koperasiA2@cocolink.id',
            'password' => Hash::make('password'),
            'role' => 'koperasi_anak',
            'parent_koperasi_id' => $induk->id,
            'phone_number' => '081234567892',
            'address' => 'Wilayah A2'
        ]);

        // 3. Pengepul / Pembeli Industri
        User::create([
            'name' => 'PT. X (Industri Briket)',
            'email' => 'ptx@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'pengepul',
            'phone_number' => '081234567893',
            'address' => 'Kawasan Industri Block X'
        ]);

        // 4. Driver
        User::create([
            'name' => 'Driver Budi Logistik',
            'email' => 'driver1@cocolink.id',
            'password' => Hash::make('password'),
            'role' => 'driver',
            'vehicle_number' => 'B 9123 COCO',
            'phone_number' => '081234567894',
            'address' => 'Pool Driver Utama'
        ]);

        // 5. Jenis Limbah Kelapa
        WasteType::create(['name' => 'Arang Batok Kelapa', 'unit' => 'Ton', 'description' => 'Arang batok standar ekspor']);
        WasteType::create(['name' => 'Sabut Kelapa (Coco Fiber)', 'unit' => 'Ton', 'description' => 'Serat sabut kelapa olahan']);
        WasteType::create(['name' => 'Air Kelapa', 'unit' => 'Liter', 'description' => 'Air kelapa murni olahan']);
        WasteType::create(['name' => 'Minyak Kelapa (VCO)', 'unit' => 'Liter', 'description' => 'Virgin Coconut Oil']);
    }
}
