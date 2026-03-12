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
        Schema::create('pos_sessions', function (Blueprint $table) {
            $table->id()->comment('ID único del turno o sesión de caja');

            $table->foreignId('pos_config_id')
                ->constrained('pos_configs')
                ->comment('Terminal utilizada en esta sesión');

            $table->foreignId('user_id')
                ->constrained('users')
                ->comment('Cajero responsable del turno');

            $table->dateTime('start_at')->comment('Fecha y hora de apertura');
            $table->dateTime('stop_at')->nullable()->comment('Fecha y hora de cierre definitivo');

            $table->enum('state', ['opening_control', 'opened', 'closing_control', 'closed'])
                ->default('opening_control')
                ->comment('Flujo de estado: Control de apertura -> Abierta -> Control de cierre -> Cerrada');

            $table->decimal('balance_start', 15, 2)->default(0.00)->comment('Dinero base dejado en caja al abrir');
            $table->decimal('balance_end_real', 15, 2)->nullable()->comment('Monto físico contado por el cajero al cerrar');

            $table->timestamps();

            // Índices de auditoría y estado
            $table->index('state');
            $table->index('start_at');
            $table->index(['pos_config_id', 'state'], 'idx_pos_session_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_sessions');
    }
};
