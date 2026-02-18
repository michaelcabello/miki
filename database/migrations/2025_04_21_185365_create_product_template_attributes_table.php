<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
//php artisan make:migration create_product_template_attributes_table
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_template_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_template_id');
            $table->unsignedBigInteger('attribute_id');

            $table->timestamps();

            $table->foreign('product_template_id', 'fk_pt')
                ->references('id')->on('product_templates')
                ->cascadeOnDelete();

            $table->foreign('attribute_id', 'fk_attr')
                ->references('id')->on('attributes')
                ->cascadeOnDelete();

            $table->unique(['product_template_id', 'attribute_id'], 'u_pta');

            $table->index(['product_template_id'], 'i_pt');
            $table->index(['attribute_id'], 'i_attr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_template_attributes');
    }
};
