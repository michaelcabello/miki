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
        Schema::create('product_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();

            $table->enum('type', ['goods', 'service', 'combo'])->default('goods');

            $table->boolean('sale_ok')->default(true);
            $table->boolean('purchase_ok')->default(false);
            $table->boolean('pos_ok')->default(true);

            $table->boolean('active')->default(true);

            $table->foreignId('uom_id')
                ->nullable()
                ->constrained('uoms')
                ->nullOnDelete();

            $table->foreignId('uom_po_id')
                ->nullable()
                ->constrained('uoms')
                ->nullOnDelete();

            $table->index(['uom_id']);
            $table->index(['uom_po_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_templates');
    }
};
