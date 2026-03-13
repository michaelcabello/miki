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
        Schema::create('comprobante_reversal_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->comment('Fiscal code (e.g., SUNAT 01)');
            $table->string('name')->comment('Description: Anulación de la operación, etc.');
            $table->enum('type', ['credit', 'debt'])->comment('Applicable to Credit or Debt notes');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comprobante_reversal_reasons');
    }
};
