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
        Schema::create('warehouse_locations', function (Blueprint $table) {
            $table->id();
            // 🔹 Código único de ubicación (ej. WH/Stock/A1)
            $table->string('code', 50);
            // 🔹 Nombre de la ubicación (ej. Aisle 1)
            $table->string('name');
            // 🔹 Nombre completo tipo árbol (ej. WH/Stock/A1/Shelf 2)
            $table->string('complete_name')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            // Árbol recursivo de ubicaciones
            $table->unsignedBigInteger('parent_id')->nullable();
            // Relación recursiva (ubicación padre)
            $table->foreign('parent_id')->references('id')->on('warehouse_locations')->nullOnDelete();
            //Relación con almacén (warehouse)
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();

            // Tipo de ubicación (igual a Odoo)
            $table->enum('usage', [
                'view',        // solo para agrupar (no almacena stock)
                'internal',    // almacén interno
                'supplier',    // proveedor
                'customer',    // cliente
                'inventory',   // ajuste de inventario
                'production',  // producción
                'transit'      // tránsito entre almacenes
            ])->default('internal');

            $table->boolean('scrap_location')->default(false)->comment('Marcar como ubicación de merma/desecho');
            // Indicador si está activa
            $table->boolean('state')->default(true);

            // 🔹 Capacidad opcional (si quieres manejar límites)
            $table->decimal('capacity', 12, 2)->nullable();
            $table->timestamps();

            $table->softDeletes();
            /*
            |--------------------------------------------------------------------------
            | Índices y Restricciones
            |--------------------------------------------------------------------------
            */

            // 🚀 REQUERIMIENTO: Unicidad de código POR ALMACÉN
            // Esto permite que el Almacén A tenga 'STOCK' y el Almacén B también tenga 'STOCK'
            $table->unique(['code', 'warehouse_id'], 'uk_loc_code_warehouse');

            $table->index(['parent_id', 'usage'], 'idx_loc_parent_usage');
            $table->index(['warehouse_id'], 'idx_loc_warehouse');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse__locations');
    }
};
