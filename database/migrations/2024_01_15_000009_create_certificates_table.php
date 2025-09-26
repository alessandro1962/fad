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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('kind', ['course', 'track'])->default('course');
            $table->unsignedBigInteger('ref_id'); // ID del corso o track
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('issued_at');
            $table->integer('hours_total')->default(0);
            $table->string('public_uid')->unique(); // Per link pubblico
            $table->string('pdf_path')->nullable();
            $table->json('metadata')->nullable(); // Dati aggiuntivi per il certificato
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            
            $table->index(['user_id', 'kind']);
            $table->index(['kind', 'ref_id']);
            $table->index(['public_uid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
