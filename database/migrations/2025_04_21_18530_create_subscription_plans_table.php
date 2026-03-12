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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id()->comment('ID único del plan de suscripción');
            $table->string('name')->comment('Nombre: Mensual, Anual Pro, etc');

            $table->integer('interval_count')
                ->default(1)
                ->comment('Cantidad de unidades de tiempo para la recurrencia');

            $table->enum('interval_unit', ['day', 'week', 'month', 'year'])
                ->default('month')
                ->comment('Unidad de tiempo: día, semana, mes o año');

            $table->boolean('state')
                ->default(true)
                ->comment('Indica si el plan puede ser asignado a nuevos productos');

            $table->integer('order')->nullable();

            $table->timestamps();

            // Índice para filtrar planes activos rápidamente
            $table->index('state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
