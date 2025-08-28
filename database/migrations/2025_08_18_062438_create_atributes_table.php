<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
//php artisan make:model Atribute -m
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('atributes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('state')->default(false);
            $table->integer('order')->nullable();


            //$table->unsignedBigInteger('groupatribute_id')->nullable();
            //$table->foreign('groupatribute_id')->references('id')->on('groupatributes')->onDelete('cascade');

            $table->foreignId('groupatribute_id')
                ->nullable()
                ->constrained('groupatributes')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atributes');
    }
};
