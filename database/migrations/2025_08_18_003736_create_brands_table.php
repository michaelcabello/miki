<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


//php artisan make:model Brand -m
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();

             $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->boolean('state')->nullable();
            $table->string('image')->nullable();
            $table->integer('order')->nullable();

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
        Schema::dropIfExists('brands');
    }
};
