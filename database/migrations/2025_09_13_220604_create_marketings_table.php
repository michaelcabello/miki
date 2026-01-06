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
        Schema::create('marketings', function (Blueprint $table) {
            $table->id();
             $table->string('titulo');
            $table->string('subject');
            $table->longText('body'); // HTML
            $table->foreignId('categorymarketing_id')->constrained()->cascadeOnDelete();
            $table->boolean('state')->default(false)->index(); // 1 activo por categoría
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketings');
    }
};
