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
        Schema::create('product_template_additional_products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_template_id');
            $table->unsignedBigInteger('additional_product_template_id');

            $table->unsignedInteger('sequence')->default(10);
            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->foreign('product_template_id', 'fk_ptap_template')
                ->references('id')
                ->on('product_templates')
                ->cascadeOnDelete();

            $table->foreign('additional_product_template_id', 'fk_ptap_additional')
                ->references('id')
                ->on('product_templates')
                ->cascadeOnDelete();

            $table->unique(
                ['product_template_id', 'additional_product_template_id'],
                'uq_pt_additional_product'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_template_additional_products');
    }
};
