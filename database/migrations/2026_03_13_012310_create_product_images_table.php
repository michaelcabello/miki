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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            // El índice ya se crea aquí automáticamente por ser foreignId + constrained
            $table->foreignId('product_variant_id') // Singular para seguir el estándar
                ->constrained('product_variants') // Referencia explícita a tu tabla en plural
                ->onDelete('cascade');

            $table->string('path');
            $table->string('alt_text')->nullable();
            // Agregamos índices manuales para optimizar búsquedas frecuentes
            $table->boolean('is_main')->default(false)->index();
            $table->integer('sort_order')->default(0)->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
