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
            $table->foreignId('account_move_id')
                ->constrained('account_moves')
                ->cascadeOnDelete();

            $table->foreignId('account_id')
                ->constrained('accounts')
                ->restrictOnDelete();

            // Si tienes partners luego lo conviertes en FK
            $table->unsignedBigInteger('partner_id')->nullable();

            $table->string('name', 255)->nullable();

            $table->decimal('debit', 14, 2)->default(0);
            $table->decimal('credit', 14, 2)->default(0);

            // para multi-moneda en líneas
            $table->decimal('amount_currency', 14, 2)->nullable();

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
