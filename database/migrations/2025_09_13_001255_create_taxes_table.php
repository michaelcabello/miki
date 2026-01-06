<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
//impuestos
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();

            $table->string('name'); // Nombre del impuesto
            $table->float('amount'); // Porcentaje o monto

            $table->enum('amount_type', ['percent', 'fixed', 'division', 'group']); // Tipo de cálculo
            $table->enum('type_tax_use', ['sale', 'purchase', 'none']); // Dónde se aplica
            $table->string('tax_scope')->nullable(); // Ámbito (flexible)

            $table->integer('sequence')->default(1); // Orden de cálculo

            // Relaciones M2O
            //$table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            //$table->foreignId('tax_group_id')->nullable()->constrained('tax_groups')->onDelete('set null');
            //$table->foreignId('cash_basis_transition_account_id')->nullable()->constrained('accounts')->onDelete('set null');

            // Flags
            $table->boolean('price_include')->default(false);
            $table->boolean('include_base_amount')->default(false);
            $table->boolean('is_base_affected')->default(false);
            $table->boolean('active')->default(true);

            $table->string('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
