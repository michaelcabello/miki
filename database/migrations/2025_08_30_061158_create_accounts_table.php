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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

             // Recursividad
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->nullOnDelete();

            // Datos contables
            $table->string('code', 20)->unique(); // código contable
            $table->string('equivalent_code', 20)->nullable(); // cuenta equivalente SUNAT
            $table->string('name', 255)->nullable();; // nombre de la cuenta

            // Relación con tipo de cuenta
            $table->foreignId('account_type_id')->constrained('account_types')->nullable(); // otra tabla

            $table->boolean('reconcile')->default(false)->nullable(); // permite conciliar?
            $table->boolean('cost_center')->default(false)->nullable(); // requiere centro de costos?
            $table->boolean('current_account')->default(false)->nullable(); // es cuenta corriente (ej. bancos)?

            $table->boolean('registro')->default(false)->nullable(); // permite saber si es la cuenta de registro

            // Amarres
           // $table->foreignId('debit_account_id')->nullable()->constrained('accounts')->nullable();
           // $table->foreignId('credit_account_id')->nullable()->constrained('accounts')->nullable();
            // Recursividad calculada
            $table->integer('depth')->default(0)->nullable();
            $table->string('path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
