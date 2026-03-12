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
        Schema::create('comprobante_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();  // FACTURA, BOLETA, NOTA_CREDITO...
            $table->string('code', 20)->unique();  // FACT, BOL, NC, GR, etc.
            $table->boolean('state')->default(true);
            $table->timestamps();

            $table->index(['state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comprobante_types');
    }
};
