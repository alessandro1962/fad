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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'expert'])->default('beginner');
            $table->integer('duration_minutes')->default(0);
            $table->integer('price_cents')->default(0);
            $table->string('currency', 3)->default('EUR');
            $table->json('tags')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('video_preview_url')->nullable();
            $table->json('features')->nullable(); // Array di caratteristiche del corso
            $table->json('requirements')->nullable(); // Prerequisiti
            $table->json('learning_objectives')->nullable(); // Obiettivi di apprendimento
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('requires_full_vision')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index(['is_active', 'published_at']);
            $table->index(['level', 'is_active']);
            $table->index(['is_featured', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
