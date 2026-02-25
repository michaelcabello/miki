<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //php artisan make:model AccountMove -m
    public function up(): void
    {
        Schema::create('account_moves', function (Blueprint $table) {
            $table->id();
              $table->foreignId('journal_id')
                ->constrained('journals')
                ->restrictOnDelete();

            $table->foreignId('pos_id')
                ->nullable()
                ->constrained('point_of_sales')
                ->nullOnDelete();

            $table->foreignId('journal_secuence_id')
                ->nullable()
                ->constrained('journal_secuences')
                ->nullOnDelete();

            // Número del documento/asiento: F001-00000001
            $table->string('name', 60)->nullable();

            // invoice|receipt|refund|debit_note|entry
            $table->string('move_type', 30)->default('entry');

            $table->date('date');

            $table->string('ref', 120)->nullable();

            // Si tienes tabla partners luego lo conviertes en FK
            $table->unsignedBigInteger('partner_id')->nullable();

            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            $table->decimal('total_debit', 14, 2)->default(0);
            $table->decimal('total_credit', 14, 2)->default(0);

            // draft|posted|cancel
            $table->string('state', 10)->default('draft');
            $table->timestamp('posted_at')->nullable();

            $table->timestamps();

            $table->index(['journal_id', 'date']);
            $table->index(['pos_id', 'date']);

            // Único global si quieres (opcional). Si prefieres permitir null repetidos:
            $table->unique('name', 'uniq_move_name');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_moves');
    }
};
