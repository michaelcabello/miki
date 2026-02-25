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
        Schema::create('point_of_sales', function (Blueprint $table) {
            $table->id();
             $table->string('code', 20)->unique();      // PV1, PV2...
            $table->string('name', 120);

            $table->string('annex_code', 10)->nullable(); // código anexo/local
            $table->string('address', 255)->nullable();

            $table->string('phone', 30)->nullable();
            $table->string('email', 120)->nullable();

            $table->boolean('state')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_of_sales');
    }
};
