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
        Schema::create('stock_moves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_picking_id')->constrained('stock_pickings')->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained('product_variants')->restrictOnDelete();
            $table->foreignId('location_from_id')->constrained('warehouse_locations')->restrictOnDelete();
            $table->foreignId('location_to_id')->constrained('warehouse_locations')->restrictOnDelete();

            $table->foreignId('purchase_order_line_id')->nullable()->constrained('purchase_order_lines')->nullOnDelete();

            $table->decimal('qty_demand', 12, 4)->comment('Cantidad solicitada');
            $table->decimal('qty_done', 12, 4)->default(0)->comment('Cantidad ejecutada');
            $table->decimal('price_unit', 15, 4)->default(0)
                ->comment('Costo unitario al momento del movimiento');

            $table->enum('state', ['draft', 'confirmed', 'assigned', 'done', 'cancel'])->default('draft');
            $table->timestamps();

            $table->index(['stock_picking_id', 'state'], 'idx_sm_picking_state');
            $table->index(['product_variant_id'], 'idx_sm_product');
            $table->index(['purchase_order_line_id'], 'idx_sm_po_line');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_moves');
    }
};
