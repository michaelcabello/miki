<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    //php artisan make:model Groupatribute -m
    public function up(): void
    {
        Schema::create('groupatributes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('state')->default(false);
            $table->integer('order')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groupatributes');
    }
};
