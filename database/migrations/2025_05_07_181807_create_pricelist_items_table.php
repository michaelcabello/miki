<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
//php artisan make:model PricelistItem -m
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pricelist_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pricelist_id')
                ->constrained('pricelists')
                ->cascadeOnDelete();

            // all / template / variant
            //$table->enum('applied_on', ['all', 'template', 'variant'])->default('all');

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories') // ajusta el nombre real
                ->nullOnDelete();

            // mejor que enum para crecer como Odoo
            $table->string('applied_on', 20)->default('all'); // all | category | template | variant

            $table->foreignId('product_template_id')
                ->nullable()
                ->constrained('product_templates')
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->cascadeOnDelete();

            $table->integer('sequence')->default(10);
            $table->decimal('min_qty', 12, 2)->default(1);

            $table->enum('compute_method', ['fixed', 'discount', 'formula'])->default('fixed');

            // fixed
            $table->decimal('fixed_price', 12, 2)->nullable();

            // discount
            $table->decimal('percent_discount', 8, 2)->nullable();

            // formula
            $table->enum('base', ['price_sale', 'cost', 'other_pricelist'])->default('price_sale');

            $table->foreignId('base_pricelist_id')
                ->nullable()
                ->constrained('pricelists')
                ->nullOnDelete();

            $table->decimal('price_multiplier', 12, 6)->nullable();
            $table->decimal('price_surcharge', 12, 2)->nullable();
            $table->decimal('rounding', 12, 6)->nullable();
            $table->decimal('min_margin', 12, 2)->nullable();
            $table->decimal('max_margin', 12, 2)->nullable();

            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();

            $table->boolean('active')->default(true);

            $table->timestamps();

            // índices cortos (evita "identifier too long")
            //$table->index(['product_category_id'], 'i_pli_pc');
            $table->index(['category_id'], 'i_pli_cat');
            $table->index(['pricelist_id', 'category_id'], 'i_pli_pl_pc');

            $table->index(['pricelist_id', 'active'], 'i_pli_pl_act');
            $table->index(['pricelist_id', 'applied_on'], 'i_pli_pl_app');
            $table->index(['product_template_id'], 'i_pli_pt');
            $table->index(['product_variant_id'], 'i_pli_pv');
            $table->index(['min_qty'], 'i_pli_minq');
            $table->index(['date_start', 'date_end'], 'i_pli_dt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricelist_items');
    }
};
