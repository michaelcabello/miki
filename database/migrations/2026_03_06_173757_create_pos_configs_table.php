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
        Schema::create('pos_configs', function (Blueprint $table) {
            $table->id()->comment('ID único de la terminal/caja');
            $table->string('name')->comment('Nombre descriptivo: Caja 01, POS Móvil, etc');
            $table->string('state')->default('active')->comment('Estado operativo de la caja');

            // Relaciones
            $table->foreignId('point_of_sales_id')
                ->constrained('point_of_sales')
                ->comment('Referencia al local o sucursal física');

            $table->foreignId('journal_id')
                ->constrained('journals')
                ->comment('Diario contable de tipo Cash donde se registrarán los movimientos');

            $table->timestamps();

            // Índices para búsquedas rápidas por local
            $table->index(['point_of_sales_id', 'state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_configs');
    }
};
