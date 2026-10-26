<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // Contoh: ORD-20261023-001
            $table->foreignId('pengepul_id')->constrained('users')->onDelete('cascade'); // PT. X, PT. Y
            $table->foreignId('waste_type_id')->constrained('waste_types')->onDelete('cascade');
            $table->decimal('quantity', 10, 2); // Jumlah pesanan
            $table->decimal('price_per_unit', 12, 2);
            $table->decimal('total_amount', 15, 2);
            $table->text('delivery_address');
            $table->enum('status', [
                'pending',        // Baru dibuat oleh Pembeli/Pengepul
                'allocated',      // Sudah dialokasikan Koperasi Induk ke Koperasi Anak
                'in_processing',  // Koperasi Anak sedang menyiapkan pasokan
                'ready_to_pick',  // Barang siap dijemput Driver
                'in_transit',     // Dalam proses pengiriman oleh Driver
                'completed',      // Pesanan telah diterima & selesai
                'cancelled'       // Dibatalkan
            ])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
