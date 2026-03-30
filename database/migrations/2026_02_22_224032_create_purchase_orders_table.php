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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Referencia: P00001 (Odoo style)
            $table->foreignId('partner_id')->constrained('partners')->comment('Proveedor'); //
            $table->foreignId('currency_id')->constrained('currencies')->comment('Moneda de la compra'); //

            // Almacén de destino (Retail: Tienda o Almacén Central)
            $table->foreignId('warehouse_id')->constrained('warehouses')->comment('Almacén donde se recibirá el stock'); //
            $table->foreignId('picking_type_id')->nullable()->constrained('stock_operation_types')->comment('Tipo de operación de recepción'); //

            $table->timestamp('date_order')->comment('Fecha de la orden');
            $table->timestamp('date_planned')->nullable()->comment('Fecha prevista de recepción');

            // Totales
            $table->decimal('amount_untaxed', 15, 4)->default(0)->comment('Subtotal sin impuestos');
            $table->decimal('amount_tax', 15, 4)->default(0)->comment('Total impuestos');
            $table->decimal('amount_total', 15, 4)->default(0)->comment('Total general');

            // Estados Odoo: draft (RFQ), sent (Enviado), purchase (PO Confirmada), done (Finalizado), cancel (Cancelado)
            $table->enum('state', ['draft', 'sent', 'to_approve', 'purchase', 'done', 'cancel'])->default('draft')->comment('Estado del flujo de compra');

            $table->text('notes')->nullable()->comment('Notas internas');
            $table->timestamps();

            // Índices cortos para evitar errores de longitud
            $table->index(['partner_id'], 'idx_po_partner');
            $table->index(['state'], 'idx_po_state');
            $table->index(['date_order'], 'idx_po_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
