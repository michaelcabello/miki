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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            // El índice ya se crea aquí automáticamente por ser foreignId + constrained
            // Variante a la que pertenece la imagen
            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->cascadeOnDelete();

            // Disco de almacenamiento de Laravel
            // Ejemplo: s3_public
            $table->string('disk')->default('s3_public');

            // Path principal que usará la app por defecto
            // En este caso apuntará normalmente a la versión medium
            $table->string('path');

            // Ruta del archivo original subido
            $table->string('original_path')->nullable();

            // Versiones optimizadas generadas con Intervention Image
            $table->string('large_path')->nullable();
            $table->string('medium_path')->nullable();
            $table->string('thumb_path')->nullable();

            // Nombre original del archivo
            $table->string('original_name')->nullable();

            // Tipo MIME del archivo original
            $table->string('mime_type')->nullable();

            // Tamaño del archivo original en bytes
            $table->unsignedBigInteger('size')->nullable();

            // Dimensiones de la imagen original
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();

            // Marca si esta imagen es la portada de la variante
            $table->boolean('is_primary')->default(false);

            // Orden visual de la imagen dentro de la galería
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            // Índices útiles para consultas frecuentes
            $table->index(['product_variant_id', 'is_primary']);
            $table->index(['product_variant_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
