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
        Schema::create('stock_pickings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('GR-0001');
            $table->enum('picking_type', ['incoming', 'outgoing', 'internal']);
            $table->foreignId('sale_order_id')->nullable()->constrained();
            $table->foreignId('purchase_order_id')->nullable()->constrained();
            $table->enum('state', ['draft', 'assigned', 'done', 'cancel'])->default('draft');
            $table->string('vehicle_plate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_pickings');
    }
};
