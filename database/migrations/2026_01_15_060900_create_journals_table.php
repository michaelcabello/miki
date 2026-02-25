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
        Schema::create('journals', function (Blueprint $table) {
            $table->id();

            // Identidad
            $table->string('name', 190);
            $table->string('code', 20); // Odoo usa 1-5 chars, tú puedes ampliar
            $table->boolean('state')->default(true); //para ver si esta activo o inactivo

            $table->boolean('use_documents')->default(false);
            //$table->foreignId('default_account_id')->nullable()->constrained('account_accounts');
            //$table->foreignId('sequence_id')->nullable()->constrained('account_sequences');
            //$table->foreignId('refund_sequence_id')->nullable()->constrained('account_sequences');
            $table->boolean('active')->default(true);


            // Tipo de diario (catálogo)
            $table->foreignId('journal_type_id')
                ->constrained('journal_types')
                ->cascadeOnDelete();



            // Moneda (si tienes currencies)
            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            // --- Defaults / cuentas por defecto (relación con accounts) ---
            // Ventas/Compras: cuenta por defecto de contrapartida (según tu lógica)
            $table->foreignId('account_id')
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            // Banco/Caja: cuentas específicas (muy típico)
            $table->foreignId('default_debit_account_id')
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            $table->foreignId('default_credit_account_id')
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            // Cuenta puente / suspenso (pagos en tránsito, conciliación, etc.)
            $table->foreignId('suspense_account_id')
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            // Cuenta de diferencia de tipo de cambio (si la usas)
            $table->foreignId('exchange_gain_account_id')
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            $table->foreignId('exchange_loss_account_id')
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            // --- Campos que “aparecen” según tipo ---
            // Bank/Cash: datos bancarios
            $table->string('bank_name', 190)->nullable();
            $table->string('bank_account_number', 60)->nullable();  // N° cuenta
            $table->string('cci', 40)->nullable();                  // Perú
            $table->string('swift', 40)->nullable();                // opcional
            $table->string('iban', 40)->nullable();                 // opcional

            // Control de numeración / facturación (sale/purchase)
            $table->boolean('use_document_sequence')->default(true);
            $table->string('document_prefix', 20)->nullable(); // por ejemplo "F001"
            $table->unsignedInteger('document_next_number')->default(1);

            // Para diario "general" puedes querer permitir asientos sin documento
            $table->boolean('allow_manual_entries')->default(true);

            // Config flexible por tipo (para no “romper” DB cuando agregas lógica)
            $table->json('settings')->nullable();



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
