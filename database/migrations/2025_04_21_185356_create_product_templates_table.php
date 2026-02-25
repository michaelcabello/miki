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
        Schema::create('product_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();

            $table->enum('type', ['goods', 'service', 'combo'])->default('goods');

            $table->boolean('sale_ok')->default(true);
            $table->boolean('purchase_ok')->default(false);
            $table->boolean('pos_ok')->default(true);

            $table->boolean('active')->default(true);

            $table->foreignId('uom_id')
                ->nullable()
                ->constrained('uoms')
                ->nullOnDelete();

            $table->foreignId('uom_po_id')
                ->nullable()
                ->constrained('uoms')
                ->nullOnDelete();

            $table->unsignedBigInteger('category_id')->nullable();

            // nombre corto para evitar "identifier too long"
            $table->foreign('category_id', 'fk_pt_category')
                ->references('id')->on('categories')
                ->nullOnDelete();


            //$table->unsignedBigInteger('sale_tax_id')->nullable();
            // $table->unsignedBigInteger('purchase_tax_id')->nullable();

            /* $table->foreign('sale_tax_id', 'fk_pt_sale_tax')
                ->references('id')->on('taxes')
                ->nullOnDelete();

            $table->foreign('purchase_tax_id', 'fk_pt_purchase_tax')
                ->references('id')->on('taxes')
                ->nullOnDelete(); */

            /*
    |--------------------------------------------------------------------------
    | Marca y Modelo
    |--------------------------------------------------------------------------
    */

            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('modello_id')->nullable();

            $table->foreign('brand_id', 'fk_pt_brand')
                ->references('id')->on('brands')
                ->nullOnDelete();

            $table->foreign('modello_id', 'fk_pt_modello')
                ->references('id')->on('modellos')
                ->nullOnDelete();

            $table->unsignedBigInteger('detraction_id')->nullable();

            // nombre corto para evitar identifier too long
            $table->foreign('detraction_id', 'fk_pt_detraction')
                ->references('id')->on('detractions')
                ->nullOnDelete();


            $table->foreignId('account_buy_id')
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            $table->foreignId('account_sell_id')
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Índices
            |--------------------------------------------------------------------------
            */

            $table->index(['uom_id'], 'i_pt_uom');
            $table->index(['uom_po_id'], 'i_pt_uompo');
            $table->index(['category_id'], 'i_pt_category');
            //$table->index(['sale_tax_id'], 'i_pt_stax');
            //$table->index(['purchase_tax_id'], 'i_pt_ptax');
            $table->index(['brand_id'], 'i_pt_brand');
            $table->index(['modello_id'], 'i_pt_modello');
            $table->index(['detraction_id'], 'i_pt_detraction');



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_templates');
    }
};
