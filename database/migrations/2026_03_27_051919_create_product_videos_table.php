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
        Schema::create('product_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_template_id')
                ->constrained('product_templates')
                ->cascadeOnDelete();

            $table->string('title')->nullable();
            $table->string('video_url'); // URL de YouTube/Vimeo o path local
            $table->enum('platform', ['youtube', 'vimeo', 'local'])->default('youtube');

            $table->integer('order')->default(0);
            $table->boolean('state')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_videos');
    }
};
