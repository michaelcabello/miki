<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//php artisan make:model Productatribute -m
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productatributes', function (Blueprint $table) {
            $table->id();

             $table->string('codigo')->unique();//se genera
            $table->string('codigo2')->nullable();//se genera
           // $table->double('price');
            $table->double('pricesale')->nullable();//precio de venta
            $table->double('pricewholesale')->nullable();//precio al por mayor
            $table->double('pricepurchase')->nullable();//precio de compra
            $table->boolean('state')->default(true);
            $table->string('slug')->unique();//no mostrare al modificar
            $table->string('titlegoogle')->nullable();
            $table->text('descriptiongoogle')->nullable();
            $table->text('keywordsgoogle')->nullable();

            $table->unsignedBigInteger('productfamilie_id')->nullable();
            $table->foreign('productfamilie_id')->references('id')->on('productfamilies')->onDelete('cascade');

             $table->string('variant_key')->nullable();//para tener los ids de las combinaciones
             $table->string('variant_name')->nullable();//para tener los names de las combinaciones
              $table->boolean('is_default')->nullable();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productatributes');
    }
};
