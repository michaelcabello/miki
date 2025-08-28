<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//php artisan make:migration create_atribute_productatribute_table --create=atribute_productatribute
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('atribute_productatribute', function (Blueprint $table) {
            $table->id();

            /* $table->unsignedBigInteger('productatribute_id');
            $table->unsignedBigInteger('atribute_id');
            $table->foreign('productatribute_id')->references('id')->on('productatributes')->onDelete('cascade');
            $table->foreign('atribute_id')->references('id')->on('atributes')->onDelete('cascade'); */

            $table->foreignId('atribute_id')
                ->constrained('atributes') // nombre de la tabla padre
                ->cascadeOnDelete();

            $table->foreignId('productatribute_id')
                ->constrained('productatributes') // nombre de la tabla padre
                ->cascadeOnDelete();

            // Si quieres evitar duplicados en la relación
            $table->unique(['atribute_id', 'productatribute_id']);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atribute_productatribute');
    }
};
