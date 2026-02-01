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
        Schema::create('attribute_value_product_template_attribute', function (Blueprint $table) {
            $table->id();
            // ⚠️ No uses constrained() acá porque genera nombres largos.
            $table->unsignedBigInteger('product_template_attribute_id');
            $table->unsignedBigInteger('attribute_value_id');

            $table->timestamps();

            // ✅ Foreign keys con nombres cortos
            $table->foreign('product_template_attribute_id', 'fk_pta')
                ->references('id')
                ->on('product_template_attributes')
                ->cascadeOnDelete();

            $table->foreign('attribute_value_id', 'fk_av')
                ->references('id')
                ->on('attribute_values')
                ->cascadeOnDelete();

            // ✅ Evitar repetidos (nombre corto)
            $table->unique(['product_template_attribute_id', 'attribute_value_id'], 'u_pta_av');

            // Índices cortos
            $table->index(['product_template_attribute_id'], 'i_pta');
            $table->index(['attribute_value_id'], 'i_av');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_value_product_template_attribute');
    }
};
