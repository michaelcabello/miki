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
        Schema::create('tax_repartition_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_id')
                ->constrained('taxes')
                ->cascadeOnDelete();

            // sale | purchase
            $table->string('type', 10);

            // base | tax
            $table->string('repartition_kind', 10);

            // 100.00 = 100%
            $table->decimal('factor_percent', 5, 2)->default(100.00);

            $table->foreignId('account_id')
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            $table->unsignedSmallInteger('sequence')->default(10);
            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->index(['tax_id', 'type', 'repartition_kind'], 'idx_tax_rep_main');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_repartition_lines');
    }
};
