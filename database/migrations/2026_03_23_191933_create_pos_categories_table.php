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
        Schema::create('pos_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('complete_name')->nullable();
            $table->string('slug')->unique();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('state')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->string('image')->nullable();

            $table->timestamps();

            $table->foreign('parent_id', 'fk_poscat_parent')
                ->references('id')
                ->on('pos_categories')
                ->nullOnDelete();

            $table->index(['state', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_categories');
    }
};
