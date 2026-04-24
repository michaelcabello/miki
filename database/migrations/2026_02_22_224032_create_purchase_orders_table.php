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
            $table->string('name')->unique();
            $table->foreignId('partner_id')->constrained('partners');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('picking_type_id')->nullable()->constrained('stock_operation_types');
            $table->foreignId('user_id')->constrained('users');

            $table->timestamp('date_order');
            $table->timestamp('date_planned')->nullable();
            $table->timestamp('date_approve')->nullable();

            $table->decimal('amount_untaxed', 15, 4)->default(0);
            $table->decimal('amount_tax', 15, 4)->default(0);
            $table->decimal('amount_total', 15, 4)->default(0);
            $table->decimal('currency_rate', 12, 6)->default(1);

            $table->enum('state', ['draft', 'sent', 'to_approve', 'purchase', 'done', 'cancel'])->default('draft');
            $table->string('pdf_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // 🚀 Tus índices reintegrados
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
