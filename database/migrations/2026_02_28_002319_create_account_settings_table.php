<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('account_settings', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | CUENTAS GENERALES (fallback máximo)
            |--------------------------------------------------------------------------
            */

            // Cuentas por cobrar (clientes)
            $table->foreignId('default_receivable_account_id')   // 12...
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            // Cuentas por pagar (proveedores)
            $table->foreignId('default_payable_account_id')      // 42...
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | INGRESOS
            |--------------------------------------------------------------------------
            */

            // Fallback general ingresos
            $table->foreignId('default_income_account_id')       // 70...
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            // 🔹 Ventas mercaderías
            $table->foreignId('default_income_goods_account_id') // 701...
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            // 🔹 Ventas servicios
            $table->foreignId('default_income_service_account_id') // 704...
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | GASTOS
            |--------------------------------------------------------------------------
            */

            // Fallback general gastos
            $table->foreignId('default_expense_account_id')      // 60/61...
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            // 🔹 Compras mercaderías
            $table->foreignId('default_expense_goods_account_id') // 601...
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            // 🔹 Compras servicios
            $table->foreignId('default_expense_service_account_id') // 63...
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | IMPUESTOS (fallback)
            |--------------------------------------------------------------------------
            */

            $table->foreignId('default_sales_tax_account_id')    // 40... IGV ventas
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            $table->foreignId('default_purchase_tax_account_id') // 40... IGV compras
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | OTROS
            |--------------------------------------------------------------------------
            */

            $table->foreignId('rounding_account_id') // redondeos
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            $table->boolean('active')->default(true);

            // Configuración adicional dinámica
            $table->json('settings')->nullable();

            $table->timestamps();

            $table->index(['active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_settings');
    }
};
