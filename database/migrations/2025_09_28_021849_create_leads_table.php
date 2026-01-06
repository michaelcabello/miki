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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
             $table->string('name')->nullable();
            $table->string('dni')->nullable()->index();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('movil')->nullable();
            $table->date('birthdate')->nullable()->index();
            $table->dateTime('dateofregistration')->index();
            $table->text('message')->nullable();
            $table->boolean('send')->default(true)->index();
            $table->unsignedInteger('contador')->default(0)->index();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stage_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
