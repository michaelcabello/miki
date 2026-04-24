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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->text('description')->nullable();
             $table->string('address')->nullable();
            $table->boolean('is_main')->default(false);
            //$table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('order')->default(0);
            $table->boolean('state')->default(true); // es para ver si esta activo o no
            // ✅ AGREGAR ubicación de stock principal (se llena con seeder/observer)
            $table->unsignedBigInteger('lot_stock_id')->nullable()
                ->comment('Ubicación principal de stock interno');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
