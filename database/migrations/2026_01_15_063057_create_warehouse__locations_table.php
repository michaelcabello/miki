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
        Schema::create('warehouse__locations', function (Blueprint $table) {
            $table->id();
            // 🔹 Código único de ubicación (ej. WH/Stock/A1)
            $table->string('code', 50)->unique();

            // 🔹 Nombre de la ubicación (ej. Aisle 1)
            $table->string('name');

            // 🔹 Nombre completo tipo árbol (ej. WH/Stock/A1/Shelf 2)
            $table->string('complete_name')->nullable();

            // 🔹 Relación con almacén (warehouse)
            $table->foreignId('warehouse_id')
                ->nullable()
                ->constrained('warehouses')
                ->cascadeOnDelete();

            // 🔹 Relación recursiva (ubicación padre)
            /* $table->foreignId('parent_id')
                ->nullable()
                ->constrained('warehouse_locations')
                ->cascadeOnDelete(); */

            //$table->unsignedBigInteger('parent_id')->nullable();
            //$table->foreign('parent_id')->references('id')->on('warehouse_locations')->onDelete('cascade');

            // 🔹 Tipo de ubicación (igual a Odoo)
            $table->enum('usage', [
                'view',        // solo para agrupar (no almacena stock)
                'internal',    // almacén interno
                'supplier',    // proveedor
                'customer',    // cliente
                'inventory',   // ajuste de inventario
                'production',  // producción
                'transit'      // tránsito entre almacenes
            ])->default('internal');

            // 🔹 Indicador si está activa
            $table->boolean('is_active')->default(true);

            // 🔹 Capacidad opcional (si quieres manejar límites)
            $table->decimal('capacity', 12, 2)->nullable();
            $table->timestamps();
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
