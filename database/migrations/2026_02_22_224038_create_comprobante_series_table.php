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
        Schema::create('comprobante_series', function (Blueprint $table) {
            $table->id();

            $table->string('name', 20); // F001, B001, etc.
            $table->foreignId('comprobante_type_id')->constrained('comprobante_types')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('journals_id')->constrained('journals')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('point_of_sales_id')->constrained('point_of_sales')->cascadeOnUpdate()->restrictOnDelete();

            // Motor de numeración
            $table->foreignId('sequences_id')->constrained('sequences')->cascadeOnUpdate()->restrictOnDelete();

            // Opcionales útiles para Perú
            $table->string('sunat_establishment_code', 10)->nullable(); // código de establecimiento, si aplica
            $table->string('sunat_emission_point', 10)->nullable();     // punto de emisión, si aplica

            $table->boolean('state')->default(true);
            $table->timestamps();

            // Evitar duplicar una serie dentro de un mismo POS y tipo doc
            $table->unique(['point_of_sales_id', 'comprobante_type_id', 'name'], 'uq_series_pos_doctype_name');

            // La secuencia no debería reutilizarse en otra serie
            $table->unique(['sequences_id'], 'uq_series_sequence');

            $table->index(['journals_id']);
            $table->index(['comprobante_type_id']);
            $table->index(['point_of_sales_id']);
            $table->index(['state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comprobante_series');
    }
};
