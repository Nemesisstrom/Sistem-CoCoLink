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
            $table->string('name'); // Contoh: Arang Batok Kelapa, Sabut Kelapa, Air Kelapa, Minyak Kelapa
            $table->string('unit')->default('kg'); // Ton, Liter, Kg
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_types');
    }
};
