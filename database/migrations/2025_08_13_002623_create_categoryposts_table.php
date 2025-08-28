<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//php artisan make:model Categorypost -m
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categoryposts', function (Blueprint $table) {
            $table->id();
             $table->string('name');
            $table->text('description')->nullable();
            $table->string('slug')->nullable();
            $table->string('image')->nullable();
             $table->integer('order')->nullable();
            $table->boolean('state')->default(false);
            $table->string('titlegoogle')->nullable();
            $table->text('descriptiongoogle')->nullable();
            $table->string('keywordsgoogle')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoryposts');
    }
};
