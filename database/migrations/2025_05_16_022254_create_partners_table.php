<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            // Identificación
            //$table->integer('depth')->nullable();
            //$table->text('path')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('partners')->onDelete('cascade');

            $table->string('name');
            $table->foreignId('company_type_id')
                ->nullable()
                ->constrained('company_types')
                ->restrictOnDelete();

            $table->foreignId('document_type_id')
                ->nullable()
                ->constrained('document_types')
                ->nullOnDelete();
            $table->string('document_number', 20)->nullable();
            $table->integer('order')->nullable();

            // Contacto
            $table->string('image')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->string('mobile', 30)->nullable();
            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();



            // Dirección (texto + FK ubigeo)
            $table->string('street')->nullable();
            $table->string('street2')->nullable();
            $table->string('zip', 10)->nullable();
            $table->string('map')->nullable();


            // ✅ UBIGEO (tus tablas tienen PK string)
            $table->string('department_id')->nullable();
            $table->string('province_id')->nullable();
            $table->string('district_id')->nullable();

            // Clasificación Odoo-like
            $table->boolean('is_customer')->default(true);
            $table->boolean('is_supplier')->default(false);
            $table->integer('customer_rank')->default(0);
            $table->integer('supplier_rank')->default(0);
            $table->boolean('status')->default(true);

            // ✅ Lista de precios por partner (descuentos automáticos)
            $table->foreignId('pricelist_id')
                ->nullable()
                ->constrained('pricelists')
                ->nullOnDelete();

            // ✅ Moneda (si tienes tabla currencies)
            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            // Datos bancarios (simple)
            $table->string('bank_account')->nullable();

            $table->boolean('portal_access')->default(false);
            $table->timestamp('portal_enabled_at')->nullable();


            $table->timestamps();

            // Índices / únicos
            $table->index(['name']);
            $table->index(['status']);
            //$table->unique(['document_type', 'document_number'], 'u_partner_doc');
            $table->unique(['document_type_id', 'document_number'], 'u_partner_doc');
            $table->index(['pricelist_id'], 'i_partner_pricelist');
            $table->index(['currency_id'], 'i_partner_currency');

            // FKs Ubigeo
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            $table->foreign('province_id')->references('id')->on('provinces')->nullOnDelete();
            $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
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
