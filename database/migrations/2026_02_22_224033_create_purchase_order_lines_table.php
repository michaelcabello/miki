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
        Schema::create('purchase_order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('product_variants')->comment('Variante de producto'); //

            $table->string('name')->comment('Descripción de la línea');
            $table->decimal('product_qty', 15, 4)->comment('Cantidad solicitada');
            $table->decimal('qty_received', 15, 4)->default(0)->comment('Cantidad ya recibida en picking');
            $table->decimal('qty_invoiced', 15, 4)->default(0)->comment('Cantidad ya facturada por el proveedor');

            // UOM específica para compras (Ej: Compras en Caja, vendes en Unidad)
            $table->foreignId('product_uom_id')->constrained('uoms')->comment('Unidad de medida de compra'); //

            $table->decimal('price_unit', 15, 4)->comment('Precio unitario pactado');
            $table->decimal('price_subtotal', 15, 4)->comment('Subtotal sin impuestos de la línea');
            $table->decimal('price_total', 15, 4)->comment('Total con impuestos de la línea');

            // Relación contable: Cuenta de gasto/costo del producto
            $table->foreignId('account_id')->nullable()->constrained('accounts')->comment('Cuenta contable de gasto'); //

            $table->timestamps();

            $table->index(['purchase_order_id'], 'idx_pol_order');
            $table->index(['product_id'], 'idx_pol_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_lines');
    }
};
