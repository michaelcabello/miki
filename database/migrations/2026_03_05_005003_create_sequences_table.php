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
        Schema::create('sequences', function (Blueprint $table) {
            $table->id();

            // Nombre humano (admin)
            $table->string('name', 150);

            // Identificador técnico único (ideal para buscar por código en el sistema)
            $table->string('code', 80)->unique();

            // Formato del número
            $table->string('prefix', 50)->default('');  // ej: F001-, B001-, PO-
            $table->string('suffix', 50)->default('');  // opcional

            // Longitud del correlativo con ceros a la izquierda (8 => 00000001)
            $table->unsignedSmallInteger('padding')->default(8);

            // Siguiente correlativo a usar
            // unsignedBigInteger por seguridad (numeración alta en POS)
            $table->unsignedBigInteger('next_no')->default(1);

            // Incremento (normalmente 1)
            $table->unsignedSmallInteger('increment_step')->default(1);

            /**
             * implementation:
             * - standard: puede tener huecos (más rápido, recomendado POS)
             * - no_gap: intenta evitar huecos (más estricto, puede bloquear más)
             */
            $table->enum('implementation', ['standard', 'no_gap'])->default('standard');

            // Si deseas reinicio anual, puedes usar use_date_ranges=true y crear tabla sequence_date_ranges
            $table->boolean('use_date_ranges')->default(false);

            // Control
            $table->boolean('state')->default(true);

            // Auditoría útil
            $table->dateTime('last_generated_at')->nullable();
            // Si tienes tabla users, puedes mantener este FK; si no, elimina esta línea y el FK.
            $table->foreignId('last_generated_by_user_id')->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->timestamps();

            // Índices útiles
            $table->index(['state']);
            $table->index(['implementation']);
            $table->index(['use_date_ranges']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sequences');
    }
};
