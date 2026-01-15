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
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_type_id')->constrained('journal_types');
            $table->string('name', 100);
            $table->string('code', 10)->unique();
            $table->boolean('use_documents')->default(false);
            //$table->foreignId('default_account_id')->nullable()->constrained('account_accounts');
            //$table->foreignId('sequence_id')->nullable()->constrained('account_sequences');
            //$table->foreignId('refund_sequence_id')->nullable()->constrained('account_sequences');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
