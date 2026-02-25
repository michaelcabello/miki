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
        Schema::create('product_template_sale_taxes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_template_id')
                ->constrained('product_templates')
                ->cascadeOnDelete();

            $table->foreignId('tax_id')
                ->constrained('taxes')
                ->restrictOnDelete();

            // opcional: prioridad/orden para UI
            $table->unsignedSmallInteger('sequence')->default(10);

            $table->timestamps();

            // evita duplicados
            $table->unique(['product_template_id', 'tax_id'], 'uniq_pt_sale_tax');
            $table->index(['tax_id'], 'idx_pt_sale_tax_tax');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_template_sale_taxes');
    }
};
