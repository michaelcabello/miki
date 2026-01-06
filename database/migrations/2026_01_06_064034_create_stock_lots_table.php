<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//php artisan make:model StockLot -m
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_lots', function (Blueprint $table) {
            $table->id();
            // Número de lote o número de serie
            // Aquí va lote o serie:
            // lote: "LOT-2026-001"
            // serie: "SN-000000123"
            $table->string('name');

            // Relación con la variante (SKU)
            $table->foreignId('productatribute_id')
                ->constrained('productatributes')
                ->cascadeOnDelete();

            // Campos de trazabilidad (como Odoo)
            //Fecha de caducidad del lote o serie.
            $table->date('expiration_date')->nullable();
            //Fecha límite recomendada de uso, antes de la expiración real.
            $table->date('use_date')->nullable();
            //Fecha en la que el lote/serie debe retirarse del inventario.
            $table->date('removal_date')->nullable();
            //Fecha para alertar al usuario antes de que algo ocurra.
            $table->date('alert_date')->nullable();
            //Referencia externa o interna.
            $table->string('reference')->nullable();
            //Notas libres del usuario.
            $table->text('note')->nullable();
            //Indica si el lote/serie está activo.
            $table->boolean('active')->default(true);

            $table->timestamps();

            // Un serial/lote no puede repetirse dentro del mismo producto
            $table->unique(['productatribute_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_lots');
    }
};
