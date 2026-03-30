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
        Schema::create('stock_operation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['incoming', 'outgoing', 'internal', 'adjustment'])
                ->comment('Dirección del flujo de stock');
            $table->string('sequence_prefix', 20)->comment('REC, SAL, INT, AJU...');
            $table->integer('sequence_number')->default(1);

            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('default_location_src_id')
                ->nullable()->constrained('warehouse_locations')->nullOnDelete();
            $table->foreignId('default_location_dest_id')
                ->nullable()->constrained('warehouse_locations')->nullOnDelete();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['warehouse_id', 'type']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_operation_types');
    }
};
