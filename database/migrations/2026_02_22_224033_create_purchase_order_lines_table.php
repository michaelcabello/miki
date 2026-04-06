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
        Schema::create('purchase_order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('product_variants');
            $table->foreignId('product_uom_id')->constrained('uoms');
            $table->string('name');
            $table->decimal('product_qty', 15, 4);
            $table->decimal('qty_received', 15, 4)->default(0);
            $table->decimal('qty_invoiced', 15, 4)->default(0);
            $table->decimal('price_unit', 15, 4);
            $table->decimal('price_subtotal', 15, 4);
            $table->decimal('price_total', 15, 4);
            $table->timestamps();

            $table->index(['purchase_order_id'], 'idx_pol_order');
            $table->index(['product_id'], 'idx_pol_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_lines');
    }
};
