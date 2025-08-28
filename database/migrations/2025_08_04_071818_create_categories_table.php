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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->boolean('state')->default(1);
            $table->integer('depth')->nullable();
            $table->text('path')->nullable();
            // $table->unsignedBigInteger('category_id')->nullable(); //activo para el recursivo
            $table->text('shortdescription')->nullable();
            $table->text('longdescription')->nullable();
            $table->integer('order')->nullable();

            $table->string('image', 2048)->default('fe/default/categories/categorydefault.jpg')->nullable();

            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');


            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('keywords')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
