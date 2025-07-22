<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
//php artisan make:model Local -m
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('locals', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->string('codigopostal')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->unique()->nullable();
            //$table->string('email')->nullable();//->unique();
            $table->string('phone')->nullable();
            $table->string('movil')->nullable();
            $table->string('anexo')->nullable();
            //$table->string('serie')->nullable(); no van en esta tabla
            //$table->string('inicia')->nullable();
            $table->boolean('state')->default(true)->nullable();
            //$table->integer('notification')->default(0);

            $table->string('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('departments');
            $table->string('province_id')->nullable();
            $table->foreign('province_id')->references('id')->on('provinces');
            $table->string('district_id')->nullable();
            $table->foreign('district_id')->references('id')->on('districts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locals');
    }
};
