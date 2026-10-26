<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique(); // Contoh: TRK-20261023-001
            $table->foreignId('order_allocation_id')->constrained('order_allocations')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('pickup_time')->nullable();
            $table->timestamp('delivered_time')->nullable();
            $table->string('proof_of_delivery')->nullable(); // Path foto bukti penyerahan barang
            $table->enum('status', [
                'searching_driver', // Mencari driver / penugasan
                'driver_assigned',   // Driver menyetujui
                'picking_up',        // Driver dalam perjalanan ke Koperasi Anak
                'delivering',        // Driver mengantar ke Pembeli/PT
                'completed'          // Pengiriman selesai
            ])->default('searching_driver');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
