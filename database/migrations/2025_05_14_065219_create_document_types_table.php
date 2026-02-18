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
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();   // RUC, DNI, CE, PAS
            $table->string('name', 80);             // Registro Único..., Documento Nacional...
            $table->unsignedInteger('length')->nullable(); // 8, 11, etc (opcional)
            $table->boolean('is_numeric')->default(true);  // (opcional)
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sequence')->default(10);
            $table->timestamps();

            $table->index(['active', 'sequence'], 'i_doc_types_act_seq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
