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
            $table->string('codsunat', 5)->nullable();
            $table->string('name', 80)->nullable();             // Registro Único..., Documento Nacional...
             $table->string('code', 20)->nullable();   // RUC, DNI, CE, PAS
            $table->unsignedInteger('length')->nullable(); // 8, 11, etc (opcional)
            $table->boolean('state')->default(true);
            $table->unsignedInteger('order')->default(10);
            $table->timestamps();

            $table->index(['state', 'order'], 'i_doc_types_state_order');
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
