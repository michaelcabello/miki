<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
//php artisan make:model Pricelist -m
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pricelists', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Default, Mayorista, POS Promo
            $table->boolean('state')->default(true);

            // lista por defecto según canal
            $table->boolean('is_default')->default(false);

            // ✅ moneda (FK a tu tabla currencies)
            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->restrictOnDelete(); // recomendado: no dejes borrar monedas si están en uso

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['is_default'], 'i_pl_default');
            $table->index(['currency_id'], 'i_pl_curr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricelists');
    }
};
