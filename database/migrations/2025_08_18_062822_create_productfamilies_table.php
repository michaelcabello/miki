<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Productfamilie;

//php artisan make:model Productfamilie -m
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productfamilies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('state')->default(true);
           // $table->boolean('simplecompound')->default(true);//0false simple, 1true compuesto

            $table->enum('tipo', [Productfamilie::PRODUCTOTERMINADO, Productfamilie::MERCADERIA, Productfamilie::SERVICIO])->default(Productfamilie::PRODUCTOTERMINADO);

            $table->boolean('haveserialnumber')->default(false);
            $table->string('gender')->nullable();//varon 1 mujer 2 unixex 3
            $table->boolean('haveserialnamber')->default(false);//si tiene serie

            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            $table->string('accounting')->nullable();//cuenta contable

       /*      $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade'); */

            $table->unsignedBigInteger('modello_id')->nullable();
            $table->foreign('modello_id')->references('id')->on('modellos')->onDelete('cascade');

            $table->unsignedBigInteger('brand_id')->nullable();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productfamilies');
    }
};
