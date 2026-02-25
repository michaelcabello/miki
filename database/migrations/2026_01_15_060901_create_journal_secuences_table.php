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
        Schema::create('journal_secuences', function (Blueprint $table) {
            $table->id();

            $table->foreignId('journal_id')
                ->constrained('journals')
                ->cascadeOnDelete();

            $table->foreignId('pos_id')
                ->constrained('point_of_sales')
                ->cascadeOnDelete();

            // invoice|receipt|refund|debit_note|entry
            $table->string('scope', 30);

            // FACTURA|BOLETA|N_CRED|N_DEB (o tu catálogo)
            $table->string('doc_type', 30);

            // F001, B001, NC001...
            $table->string('series', 10);

            // prefijo (por defecto suele ser series + '-')
            $table->string('prefix', 20);

            $table->unsignedInteger('next_number')->default(1);
            $table->unsignedSmallInteger('padding')->default(8);

            $table->boolean('reset_yearly')->default(false);
            $table->boolean('reset_monthly')->default(false);

            $table->boolean('active')->default(true);

            $table->timestamps();

            // Evita duplicados dentro de un mismo PV y diario
            $table->unique(['journal_id', 'pos_id', 'scope', 'series'], 'uniq_jseq');
            $table->index(['journal_id', 'pos_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_secuences');
    }
};
