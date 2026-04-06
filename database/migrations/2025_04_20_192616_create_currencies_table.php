<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//php artisan make:model Currency -m
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();

            $table->string('name', 10); // Ej: USD, PEN, EUR
            $table->string('description')->nullable(); // Ej: Dólar Americano
            $table->string('abbreviation', 5)->nullable(); // Ej: $, S/, €

            // Configuración de Visualización (Nivel Odoo)
            $table->enum('symbol_position', ['before', 'after'])->default('before');
            $table->integer('decimal_places')->default(2);
            $table->decimal('rounding', 12, 6)->default(0.010000);

            // Estados
            $table->boolean('principal')->default(false); // La moneda base del sistema
            $table->boolean('active')->default(true); // Si la moneda está disponible para su uso

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
