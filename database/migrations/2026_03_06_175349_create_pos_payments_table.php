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
        Schema::create('pos_payments', function (Blueprint $table) {
           $table->id();
            $table->foreignId('pos_session_id')
                  ->constrained('pos_sessions')
                  ->onDelete('cascade')
                  ->comment('Sesión a la que pertenece el movimiento');

            $table->decimal('amount', 15, 2)->comment('Monto: Positivo para ingresos, Negativo para egresos');

            $table->enum('payment_type', ['sale', 'inflow', 'outflow'])
                  ->comment('Categoría: Venta, Ingreso extra, Salida de Caja Chica');

            $table->foreignId('account_id')
                  ->constrained('accounts')
                  ->comment('Cuenta contable de contrapartida (Ej: Ingresos por ventas o Gasto de limpieza)');

            $table->string('description')->nullable()->comment('Glosa o justificación del movimiento');

            $table->timestamps();

            // Índices para reportes financieros
            $table->index(['pos_session_id', 'payment_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_payments');
    }
};
