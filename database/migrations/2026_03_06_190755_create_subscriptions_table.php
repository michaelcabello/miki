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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id()->comment('ID del contrato de suscripción');

            $table->foreignId('partner_id')->constrained()->comment('Cliente/Tercero titular');

            $table->foreignId('product_template_id')
                ->constrained('product_templates')
                ->cascadeOnDelete();

            $table->foreignId('subscription_plan_id')->constrained()->comment('Frecuencia de pago pactada');

            $table->decimal('recurring_price', 15, 2)->comment('Precio pactado (protegido contra cambios en el catálogo)');

            $table->date('start_date')->comment('Fecha de inicio del servicio');

            $table->date('next_billing_date')
                ->comment('FECHA CLAVE: Cuándo debe el sistema generar la siguiente factura');

            $table->enum('status', ['draft', 'active', 'paused', 'cancelled'])
                ->default('active')
                ->comment('Estado del contrato');

            $table->foreignId('last_move_id')
                ->nullable()
                ->comment('Referencia al último asiento contable/factura generado');

            $table->timestamps();
            $table->softDeletes()->comment('Borrado lógico para conservar historial de contratos');

            // ÍNDICES CRÍTICOS
            $table->index('next_billing_date'); // Vital para el Cron Job diario
            $table->index('status');
            $table->index(['partner_id', 'status']); // Para ver suscripciones de un cliente
            $table->index(['product_template_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
