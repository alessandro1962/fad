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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['video', 'slide', 'quiz', 'pdf', 'link', 'text'])->default('video');
            $table->integer('order')->default(0);
            $table->integer('duration_minutes')->default(0);
            $table->json('payload')->nullable(); // Metadata specifici per tipo (URL video, contenuto slide, etc.)
            $table->boolean('is_active')->default(true);
            $table->boolean('is_free')->default(false);
            $table->timestamps();
            
            $table->index(['module_id', 'order']);
            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
