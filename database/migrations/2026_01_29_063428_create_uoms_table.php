<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//php artisan make:model Uom -m
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('uoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uom_category_id')
                ->constrained('uom_categories')
                ->cascadeOnDelete();

            $table->string('name');         // Unidad, Docena, kg, g, L, ml...
            $table->string('symbol')->nullable(); // u, dz, kg, g, L, ml
            $table->boolean('active')->default(true);

            // "reference", "bigger", "smaller" (como Odoo)
            $table->enum('uom_type', ['reference', 'bigger', 'smaller'])->default('reference');

            // factor respecto a la referencia:
            // 1 "bigger" = factor * reference
            // 1 "smaller" = factor * reference (factor < 1)
            $table->decimal('factor', 18, 8)->default(1);

            $table->integer('rounding')->default(1); // opcional: redondeo (ej. vender solo enteros)
            $table->integer('sort_order')->nullable();

            $table->timestamps();

            $table->unique(['uom_category_id', 'name']); // evita duplicados dentro de categoría
            $table->index(['uom_category_id', 'uom_type']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uoms');
    }
};
