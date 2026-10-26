<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Buat Daftar Permissions
        $permissions = [
            'manage_all_accounts',
            'system_configuration',
            'audit_platform',
            'verify_members',
            'validate_orders',
            'allocate_to_child_coop',
            'assign_driver',
            'view_reports',
            'manage_stock_capacity',
            'accept_reject_allocation',
            'prepare_goods',
            'update_supply_status',
            'create_orders',
            'track_order_status',
            'confirm_receipt',
            'view_invoices',
            'accept_delivery_task',
            'pickup_goods',
            'update_shipping_status',
            'upload_proof_of_delivery',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Buat Roles & Assign Permissions
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $roleSuperAdmin->givePermissionTo(Permission::all());

        $roleInduk = Role::firstOrCreate(['name' => 'koperasi_induk']);
        $roleInduk->givePermissionTo([
            'verify_members', 'validate_orders', 'allocate_to_child_coop',
            'assign_driver', 'view_reports', 'track_order_status'
        ]);

        $roleAnak = Role::firstOrCreate(['name' => 'koperasi_anak']);
        $roleAnak->givePermissionTo([
            'manage_stock_capacity', 'accept_reject_allocation',
            'prepare_goods', 'update_supply_status'
        ]);

        $rolePengepul = Role::firstOrCreate(['name' => 'pengepul']);
        $rolePengepul->givePermissionTo([
            'create_orders', 'track_order_status', 'confirm_receipt', 'view_invoices'
        ]);

        $roleDriver = Role::firstOrCreate(['name' => 'driver']);
        $roleDriver->givePermissionTo([
            'accept_delivery_task', 'pickup_goods', 'update_shipping_status', 'upload_proof_of_delivery'
        ]);

        // 3. Buat Akun Default (Safe Re-seed menggunakan firstOrCreate)
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@cocolink.id'],
            [
                'name' => 'Super Admin CoCoLink',
                'password' => Hash::make('password123'),
                'phone_number' => '081111111111',
                'address' => 'Kantor Pusat Management CoCoLink',
            ]
        );
        $superAdmin->assignRole($roleSuperAdmin);

        $induk = User::firstOrCreate(
            ['email' => 'induk@cocolink.id'],
            [
                'name' => 'Koperasi Induk Utama (Central Ops)',
                'password' => Hash::make('password123'),
                'phone_number' => '081234567890',
                'address' => 'Kantor Pusat Koperasi Induk',
            ]
        );
        $induk->assignRole($roleInduk);

        $a1 = User::firstOrCreate(
            ['email' => 'koperasiA1@cocolink.id'],
            [
                'name' => 'Koperasi Anak A1 (Bogor)',
                'password' => Hash::make('password123'),
                'parent_koperasi_id' => $induk->id,
                'phone_number' => '081234567891',
                'address' => 'Gudang Pasokan Wilayah A1 - Bogor',
            ]
        );
        $a1->assignRole($roleAnak);

        $a2 = User::firstOrCreate(
            ['email' => 'koperasiA2@cocolink.id'],
            [
                'name' => 'Koperasi Anak A2 (Sukabumi)',
                'password' => Hash::make('password123'),
                'parent_koperasi_id' => $induk->id,
                'phone_number' => '081234567892',
                'address' => 'Gudang Pasokan Wilayah A2 - Sukabumi',
            ]
        );
        $a2->assignRole($roleAnak);

        $pengepul = User::firstOrCreate(
            ['email' => 'ptx@gmail.com'],
            [
                'name' => 'PT. X (Industri Briket)',
                'password' => Hash::make('password123'),
                'phone_number' => '081234567893',
                'address' => 'Kawasan Industri Block X, Cikarang',
            ]
        );
        $pengepul->assignRole($rolePengepul);

        $driver = User::firstOrCreate(
            ['email' => 'driver1@cocolink.id'],
            [
                'name' => 'Driver Budi Logistik',
                'password' => Hash::make('password123'),
                'vehicle_number' => 'B 9123 COCO',
                'phone_number' => '081234567894',
                'address' => 'Pool Armada Logistik Utama',
            ]
        );
        $driver->assignRole($roleDriver);
    }
}
