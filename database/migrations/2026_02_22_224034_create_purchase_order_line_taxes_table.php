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
        Schema::create('purchase_order_line_taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_line_id')->constrained('purchase_order_lines', 'id', 'fk_pol_tax')->cascadeOnDelete();
            $table->foreignId('tax_id')->constrained('taxes')->comment('Impuesto aplicado'); //

            // Índice único para evitar duplicados del mismo impuesto en la misma línea
            $table->unique(['purchase_order_line_id', 'tax_id'], 'unq_pol_tax');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_line_taxes');
    }
};
