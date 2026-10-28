<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('koperasi_anak_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('waste_type_id')->constrained('waste_types')->onDelete('cascade');
            $table->decimal('stock_in', 12, 2)->default(0);  // Pasokan Masuk dari Petani
            $table->decimal('stock_out', 12, 2)->default(0); // Keluar untuk Pengiriman
            $table->decimal('current_stock', 12, 2);          // Saldo Stok
            $table->date('entry_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_stocks');
    }
};
