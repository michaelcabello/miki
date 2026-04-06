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
        Schema::create('document_settings', function (Blueprint $table) {
            $table->id();
            // Relación con tu tabla de tipos de comprobantes
            $table->foreignId('comprobante_type_id')
                ->constrained('comprobante_types')
                ->onDelete('cascade');

            $table->string('template_name'); // Nombre visual: "Indigo Modern", "Odoo Classic"
            $table->string('blade_path');    // Ruta: "admin.pdf.templates.invoice.modern"
            $table->string('paper_size')->default('A4'); // A4, 80mm, Letter
            $table->string('primary_color')->default('#4f46e5');

            // Campos solicitados
            $table->integer('order')->default(0); // Para ordenar las opciones en la interfaz
            $table->boolean('activate')->default(false); // Define si esta es la plantilla activa

            $table->timestamps();

            // Índice para mejorar la búsqueda de la plantilla activa por tipo
            $table->index(['comprobante_type_id', 'activate']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_settings');
    }
};
