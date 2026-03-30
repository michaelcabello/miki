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
        Schema::create('stock_quants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('warehouse_locations')->cascadeOnDelete();
            $table->foreignId('lot_id')->nullable()->constrained('stock_lots')->nullOnDelete();

            $table->decimal('quantity', 12, 4)->default(0);
            $table->decimal('reserved_quantity', 12, 4)->default(0);
            $table->timestamp('last_count_date')->nullable();
            $table->timestamps();

            $table->unique(['product_variant_id', 'location_id', 'lot_id'], 'uq_quant_product_location_lot');
            $table->index(['location_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_quants');
    }
};
