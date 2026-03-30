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
        Schema::create('pos_orders', function (Blueprint $table) {
            $table->id();
            $table->string('pos_reference')->unique()->comment('Reference: POS/001/0001');
            $table->foreignId('partner_id')->nullable()->constrained();
            //$table->foreignId('pos_session_id')->constrained();
            $table->enum('state', ['draft', 'paid', 'done', 'cancel'])->default('draft');
            $table->decimal('amount_total', 15, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_orders');
    }
};
