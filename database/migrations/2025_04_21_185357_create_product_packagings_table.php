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
        Schema::create('product_packagings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_template_id')
                ->constrained('product_templates')
                ->cascadeOnDelete();

            $table->string('name'); // Pack, Caja, Pallet
            $table->decimal('qty', 12, 4); // cuántas unidades base contiene (ej: 6, 12, 120)

            // opcional: barcode por packaging
            $table->string('barcode')->nullable()->unique();

            // opcional: precio específico si vendes por empaque
            $table->decimal('price_sale', 12, 2)->nullable();

            $table->boolean('active')->default(true);
            $table->integer('sort_order')->nullable();
            $table->timestamps();

            $table->index(['product_template_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_packagings');
    }
};
