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
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Codice univoco del badge
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Nome icona o URL
            $table->string('color', 7)->default('#FFC857'); // Colore hex
            $table->json('criteria')->nullable(); // Criteri per ottenere il badge
            $table->enum('category', ['achievement', 'milestone', 'special', 'social'])->default('achievement');
            $table->boolean('is_active')->default(true);
            $table->integer('rarity')->default(1); // 1=comune, 2=raro, 3=epico, 4=leggendario
            $table->timestamps();
            
            $table->index(['category', 'is_active']);
            $table->index(['rarity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
