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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_template_id')
                ->constrained('product_templates')
                ->cascadeOnDelete();

            // Identificadores
            $table->string('sku')->unique();               // CAM-M-NEG
            $table->string('barcode')->nullable()->unique();

            // ✅ NUEVO: tracking (inventario)
            $table->enum('tracking', ['none', 'quantity', 'lot', 'serial'])->default('quantity');

            // ✅ NUEVO: referencia (interno / proveedor / etc.)
            $table->string('reference')->nullable()->index();

            // Precios (puedes ajustar según tu negocio)
            $table->decimal('price_sale', 12, 2)->nullable();
            $table->decimal('price_wholesale', 12, 2)->nullable();
            $table->decimal('price_purchase', 12, 2)->nullable();

            // Estado
            $table->boolean('active')->default(true);

            // ✅ Para el listado principal (precio representativo / referencia)
            $table->boolean('is_default')->default(false);

            // ✅ Firma estable de combinación para evitar duplicados:
            // ejemplo: "1:10|2:21" (attribute_id:value_id ordenados por attribute_id)
            $table->string('combination_key')->nullable();

            // Nombre opcional para UI (cache): "M - Negro"
            $table->string('variant_name')->nullable();


            $table->timestamps();

            $table->index(['product_template_id']);
            $table->index(['product_template_id', 'is_default']);
            $table->index(['product_template_id', 'active']);

            // Si usas combination_key, evita duplicados por template:
            $table->unique(['product_template_id', 'combination_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
