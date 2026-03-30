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
            /* $table->string('name')->unique()->comment('GR-0001');
            $table->enum('picking_type', ['incoming', 'outgoing', 'internal']);
            $table->foreignId('sale_order_id')->nullable()->constrained();
            $table->foreignId('purchase_order_id')->nullable()->constrained();
            $table->enum('state', ['draft', 'assigned', 'done', 'cancel'])->default('draft');
            $table->string('vehicle_plate')->nullable(); */
            $table->string('name')->unique()->comment('REC/0001, SAL/0001...');

            $table->foreignId('operation_type_id')
                ->constrained('stock_operation_types')->restrictOnDelete();
            $table->foreignId('location_from_id')
                ->constrained('warehouse_locations')->restrictOnDelete();
            $table->foreignId('location_to_id')
                ->constrained('warehouse_locations')->restrictOnDelete();

            $table->foreignId('partner_id')->nullable()->constrained('partners')->nullOnDelete();
            $table->foreignId('sale_order_id')->nullable()->constrained('sale_orders')->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();

            $table->enum('state', ['draft', 'confirmed', 'assigned', 'done', 'cancel'])->default('draft');
            $table->date('scheduled_date')->nullable();
            $table->date('date_done')->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['operation_type_id', 'state']);
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
