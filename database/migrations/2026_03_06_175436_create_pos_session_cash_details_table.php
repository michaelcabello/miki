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
        Schema::create('pos_session_cash_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_session_id')
                ->constrained('pos_sessions')
                ->onDelete('cascade')
                ->comment('Referencia al arqueo de la sesión');

            $table->decimal('coin_value', 10, 2)->comment('Valor nominal del billete o moneda');
            $table->integer('number_of_units')->comment('Cantidad de unidades contadas');
            $table->decimal('total_value', 15, 2)->comment('Subtotal: valor * unidades');

            $table->timestamps();

            $table->index('pos_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_session_cash_details');
    }
};
