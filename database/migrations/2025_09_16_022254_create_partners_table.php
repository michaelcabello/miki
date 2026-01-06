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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
             // Identificación
            $table->string('name'); // Nombre de empresa o persona
            $table->string('company_type')->default('company'); // 'person' o 'company'
            $table->string('document_type')->nullable(); // RUC, DNI, CE, Pasaporte
            $table->string('document_number')->nullable()->unique();

            // Contacto
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('website')->nullable();

            // Dirección
            $table->string('street')->nullable();
            $table->string('street2')->nullable();
            $table->string('department')->nullable();
            $table->string('province')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            //$table->string('country')->nullable();

            // Clasificación
            $table->integer('customer_rank')->default(0); // # veces usado como cliente
            $table->integer('supplier_rank')->default(0); // # veces usado como proveedor
            $table->boolean('is_company')->default(false); // Si es empresa
            $table->boolean('active')->default(true);

            // Datos financieros básicos
            $table->string('bank_account')->nullable(); // Nº cuenta bancaria
            $table->string('currency')->nullable()->default('PEN'); // Moneda principal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
