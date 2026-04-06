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
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('currency_id')->constrained();
            $table->decimal('currency_rate', 12, 4)->default(1);
            // Odoo usa 'rate' como base inversa, pero para Perú es más claro así:
            $table->decimal('buy_rate', 12, 4);  // Tipo de Cambio Compra
            $table->decimal('sell_rate', 12, 4); // Tipo de Cambio Venta
            $table->decimal('official_rate', 12, 4)->nullable(); // Opcional: SUNAT/SBS

            $table->date('date'); // Fecha de vigencia

            $table->timestamps();

            $table->unique(['currency_id', 'date']); // No duplicar tasas el mismo día
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
    }
};
