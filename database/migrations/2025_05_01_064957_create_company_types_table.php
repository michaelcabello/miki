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
        Schema::create('company_types', function (Blueprint $table) {
            $table->id();
             $table->string('code', 20)->unique(); // company, person (y futuros)
            $table->string('name', 50);           // Empresa, Persona
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sequence')->default(10);
            $table->timestamps();

            $table->index(['active', 'sequence'], 'i_company_types_act_seq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_types');
    }
};
