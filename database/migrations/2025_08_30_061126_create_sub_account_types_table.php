<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
//php artisan make:model SubAccountType -m
//estamos creando el modelo SubAccountType y tabla sub_account_types
//la tabla estara en plural
//cuando son relaciones de muchos a muchos la tabla generada tiene guion bajo y es en  singular
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sub_account_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('order')->nullable(); // orden de presentación
            $table->boolean('state')->default(true); // activo o inactivo
            $table->foreign('account_type_id')
                  ->references('id')
                  ->on('account_types')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_account_types');
    }
};
