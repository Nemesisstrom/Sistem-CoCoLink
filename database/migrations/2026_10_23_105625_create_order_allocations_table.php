<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('koperasi_anak_id')->constrained('users')->onDelete('cascade'); // Koperasi A1, A2
            $table->decimal('allocated_quantity', 12, 2); // Contoh: 30 Ton untuk A1, 20 Ton untuk A2
            $table->enum('status', [
                'assigned',   // Alokasi ditugaskan dari Koperasi Induk
                'accepted',   // Disetujui Koperasi Anak
                'ready',      // Pasokan siap di gudang Koperasi Anak
                'picked_up',  // Sudah diambil Driver
                'delivered'   // Sampai di PT/Pengepul
            ])->default('assigned');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_allocations');
    }
};
