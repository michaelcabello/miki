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
        Schema::create('pos_category_product_template', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_template_id');
            $table->unsignedBigInteger('pos_category_id');

            $table->timestamps();

            $table->foreign('product_template_id', 'fk_pcpt_template')
                ->references('id')
                ->on('product_templates')
                ->cascadeOnDelete();

            $table->foreign('pos_category_id', 'fk_pcpt_poscat')
                ->references('id')
                ->on('pos_categories')
                ->cascadeOnDelete();

            $table->unique(
                ['product_template_id', 'pos_category_id'],
                'uq_pt_poscat'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_category_product_template');
    }
};
