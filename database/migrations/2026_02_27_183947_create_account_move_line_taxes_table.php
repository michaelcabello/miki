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
        Schema::create('account_move_line_taxes', function (Blueprint $table) {
            $table->id();

               $table->foreignId('move_line_id')
                ->constrained('account_move_lines')
                ->cascadeOnDelete();

            $table->foreignId('tax_id')
                ->constrained('taxes')
                ->restrictOnDelete();

            // Para auditar / congelar el cálculo
            $table->decimal('base_amount', 14, 2)->nullable();
            $table->decimal('tax_amount', 14, 2)->nullable();

            $table->unsignedSmallInteger('sequence')->default(10);

            $table->timestamps();

            $table->unique(['move_line_id', 'tax_id'], 'uniq_move_line_tax');
            $table->index(['tax_id'], 'idx_ml_tax_tax');
            $table->index(['move_line_id'], 'idx_ml_tax_line');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_move_line_taxes');
    }
};
