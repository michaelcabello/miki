<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
//php artisan make:model Company -m
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('ruc')->nullable();
            $table->string('razonsocial')->nullable();
            $table->string('nombrecomercial')->nullable();
            $table->string('razonsocialaws')->nullable(); //este campo se crea para que cuando cambien algo en la razon social principal no afecte a la generacion de imagenes en aws
            //este campo no se muestra estara oculto para el usuario
            $table->string('direccion')->nullable();
            $table->string('celular')->nullable();
            $table->string('telefono')->nullable();

            $table->string('correo')->nullable();
            $table->string('smtp')->nullable();
            $table->string('password')->nullable();
            $table->string('puerto')->nullable();


            $table->string('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('departments');
            $table->string('province_id')->nullable();
            $table->foreign('province_id')->references('id')->on('provinces');
            $table->string('district_id')->nullable();
            $table->foreign('district_id')->references('id')->on('districts');
            $table->string('ubigeo')->nullable();
            //$table->string('urbanizacion')->nullable();

            $table->string('logo')->nullable();
            $table->string('soluser')->nullable();
            $table->string('solpass')->nullable();
            $table->text('certificado')->nullable();
            $table->string('certificate_path')->nullable();
            $table->date('fechainiciocertificado')->nullable();
            $table->date('fechafincertificado')->nullable();
            $table->string('cliente_id')->nullable();
            $table->string('cliente_secret')->nullable();
            $table->boolean('production')->default(0); //si no
            $table->boolean('state')->default(1);
            $table->string('ublversion')->nullable();
            $table->double('detraccion', 10, 4)->nullable();
            $table->string('pago')->nullable(); //controlaremos si pago o no pago, guardaremos encriptado
            //$table->foreignId('currency_id')->constrained();//moneda por defecto para los comprobantes
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');

            // 🔹 Configuración de decimales por Tenant
            $table->integer('decimal_purchase')->default(2)->comment('Decimales para precios de compra');
            $table->integer('decimal_sale')->default(2)->comment('Decimales para precios de venta');
            $table->integer('decimal_stock')->default(2)->comment('Decimales para cantidades de inventario');

            // 🔹 Tipo de redondeo (Odoo style)
            // 'round_per_line': Calcula impuestos por cada fila
            // 'round_globally': Suma todas las bases y calcula el impuesto al final
            $table->enum('tax_calculation_rounding', ['round_per_line', 'round_globally'])->default('round_per_line');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
