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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('slug');//slug
            $table->mediumText('resumen')->nullable();
            $table->text('iframe')->nullable();
            $table->text('body')->nullable();
            $table->text('video1')->nullable();
            $table->text('video2')->nullable();
            $table->text('video3')->nullable();
            $table->text('video4')->nullable();
            $table->boolean('state')->nullable();
            $table->boolean('home')->nullable();

            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('categorypost_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('categorypost_id')->references('id')->on('categoryposts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('titlegoogle')->nullable();
            $table->text('descriptiongoogle')->nullable();
            $table->string('keywordsgoogle')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
