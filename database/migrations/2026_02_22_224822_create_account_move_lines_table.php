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
        Schema::create('account_move_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_move_id')->constrained('account_moves')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounts')->restrictOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained();

            // Si tienes partners luego lo conviertes en FK
            $table->unsignedBigInteger('partner_id')->nullable();

            $table->string('name')->nullable()->comment('Label or description');
            $table->decimal('quantity', 15, 4)->default(1);
            $table->decimal('price_unit', 15, 4);
            $table->decimal('discount', 15, 4)->default(0);

            $table->decimal('debit', 15, 4)->default(0);
            $table->decimal('credit', 15, 4)->default(0);

            // para multi-moneda en líneas
            $table->decimal('amount_currency', 15, 4)->nullable();

            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            // orden dentro del asiento
            $table->unsignedSmallInteger('sequence')->default(1);

            $table->timestamps();

            $table->index(['account_move_id']);
            $table->index(['account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_move_lines');
    }
};
