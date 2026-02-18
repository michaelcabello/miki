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
        Schema::create('detractions', function (Blueprint $table) {
            $table->id();
            // Catálogo SUNAT (ej: 014, 037, etc.)
            $table->string('code', 10)->unique();

            // Nombre visible: "Transporte de carga", "Servicios empresariales", etc.
            $table->string('name');

            // % detracción: 4.00, 10.00, 12.00...
            $table->decimal('rate', 6, 2);

            // Reglas opcionales
            $table->decimal('min_amount', 12, 2)->nullable(); // si aplica mínimo
            $table->boolean('applies_to_sale')->default(true);
            $table->boolean('applies_to_purchase')->default(false);

            $table->boolean('active')->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['active'], 'i_det_active');
            $table->index(['rate'], 'i_det_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detractions');
    }
};
