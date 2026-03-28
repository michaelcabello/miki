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
        Schema::create('product_brochures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_template_id')
                ->constrained('product_templates')
                ->cascadeOnDelete();

            $table->string('name'); // Ejemplo: "Manual de Usuario", "Ficha Técnica"
            $table->string('file_path'); // Ruta del archivo en el storage

            $table->integer('order')->default(0);
            $table->boolean('state')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_brochures');
    }
};
