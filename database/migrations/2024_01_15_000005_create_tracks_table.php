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
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('rules')->nullable(); // Regole per suggerimenti automatici
            $table->json('target_audience')->nullable(); // Cluster target (professioni, motivazioni, etc.)
            $table->integer('estimated_duration_hours')->default(0);
            $table->foreignId('certificate_template_id')->nullable()->constrained('certificate_templates')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_auto_assign')->default(false); // Assegnazione automatica
            $table->timestamps();
            
            $table->index(['is_active', 'is_auto_assign']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
