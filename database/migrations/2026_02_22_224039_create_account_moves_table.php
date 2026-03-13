<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //php artisan make:model AccountMove -m
    public function up(): void
    {
        Schema::create('account_moves', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Fiscal number: F001-00000001');
            $table->enum('move_type', [
                'out_invoice', // Factura de cliente (Venta)
                'in_invoice',  // Factura de proveedor (Compra)
                'out_refund',  // Nota de crédito enviada al cliente (Anulación de venta)
                'in_refund',   // Nota de crédito recibida del proveedor (Anulación de compra)
                'entry'        // Asiento contable manual / Operaciones diversas
            ])->comment('Define la naturaleza contable y dirección del flujo de dinero (Venta, Compra, Devolución o Ajuste)');

            $table->enum('state', ['draft', 'posted', 'cancel'])->default('draft')->index();
            $table->foreignId('journal_id')->constrained('journals')->restrictOnDelete();
            $table->foreignId('point_of_sale_id')->nullable()->constrained('point_of_sales')->nullOnDelete();
            $table->foreignId('partner_id')->constrained('partners')->comment('Customer or supplier reference');
            //$table->foreignId('comprobante_series_id')->constrained('comprobante_series')->comment('Used series for this document');

            $table->foreignId('comprobante_series_id')
                ->constrained('comprobante_series')
                ->comment('Referencia fija a la serie utilizada para este comprobante');

            // Totales
            $table->decimal('amount_untaxed', 15, 4)->default(0);
            $table->decimal('amount_tax', 15, 4)->default(0);
            $table->decimal('amount_total', 15, 4)->default(0);

            $table->date('date')->comment('Fiscal issuance date');

            // Orígenes opcionales
            $table->foreignId('sale_order_id')->nullable()->constrained('sale_orders');
            $table->foreignId('pos_order_id')->nullable()->constrained('pos_orders');
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders');

            // Referencia para Notas de Crédito
            $table->foreignId('reversed_entry_id')->nullable()->constrained('account_moves')->nullOnDelete();
            $table->foreignId('reversal_reason_id')->nullable()->constrained('comprobante_reversal_reasons');

            /* $table->foreignId('secuence_id')
                ->nullable()
                ->constrained('secuences')
                ->nullOnDelete(); */


            $table->string('ref', 120)->nullable();

            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();

            $table->decimal('total_debit', 15, 4)->default(0);
            $table->decimal('total_credit', 15, 4)->default(0);


            $table->timestamp('posted_at')->nullable();

            $table->timestamps();

            $table->index(['journal_id', 'date']);
            $table->index(['point_of_sale_id', 'date']);

            // Único global si quieres (opcional). Si prefieres permitir null repetidos:
            $table->unique('name', 'uniq_move_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_moves');
    }
};
