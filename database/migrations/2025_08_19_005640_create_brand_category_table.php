<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


//php artisan make:migration create_brand_category_table --create=brand_category
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brand_category', function (Blueprint $table) {
            //$table->id();

            /* $table->unsignedBigInteger('brand_id');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');

            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade'); */

            $table->foreignId('brand_id')
                ->constrained('brands')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_category');
    }
};
