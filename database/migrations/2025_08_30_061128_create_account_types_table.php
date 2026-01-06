<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
//php artisan make:model AccountType -m
//estamos creando el modelo AccountType y tabla account_types (plural)
//la tabla estara en plural
//cuando son relaciones de muchos a muchos la tabla generada tiene guion bajo y es en  singular
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable(); // por cobrar, banco y efectivo, activos no correintes, prepagos, activos fijos
            $table->text('note')->nullable();
            $table->integer('order')->nullable(); // orden de presentación
            //$table->foreignId('sub_account_type_id')->constrained('sub_account_types')->nullable(); // otra tabla
            $table->boolean('state')->default(true); // activo o inactivo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_types');
    }
};
