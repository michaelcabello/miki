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
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')
                ->constrained('attributes')
                ->cascadeOnDelete();

            $table->string('name');                        // S, M, L, Rojo, Negro...
            $table->boolean('active')->default(true);
            $table->integer('sort_order')->nullable();

            // Opcional: si el valor agrega precio (ej: XL +10)
            $table->decimal('extra_price', 12, 2)->default(0);

            $table->timestamps();

            $table->unique(['attribute_id', 'name']);
            $table->index(['attribute_id', 'sort_order']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};
