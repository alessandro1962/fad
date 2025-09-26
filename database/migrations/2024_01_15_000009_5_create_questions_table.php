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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->text('text');
            $table->enum('type', ['multiple_choice', 'single_choice', 'true_false', 'text', 'number'])->default('single_choice');
            $table->json('options')->nullable(); // Opzioni per domande a scelta multipla
            $table->json('correct_answer')->nullable(); // Risposta corretta
            $table->integer('score')->default(1); // Punteggio della domanda
            $table->text('explanation')->nullable(); // Spiegazione della risposta
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['quiz_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
