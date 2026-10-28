<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Kode Unik Komoditas
            $table->string('name'); // Contoh: Arang Batok, Sabut Kelapa, Air Kelapa
            $table->decimal('price_per_unit', 12, 2)->default(0); // Harga standar per unit
            $table->enum('unit', ['ton', 'kg', 'liter', 'pcs'])->default('ton'); // Satuan tertinggi: Ton
            $table->string('icon')->default('bi-box'); // Ikon Bootstrap
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_types');
    }
};
