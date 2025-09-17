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
        Schema::table('certificate_templates', function (Blueprint $table) {
            // Aggiungi course_id
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade');
            
            // Aggiungi settings JSON
            $table->json('settings')->nullable();
            
            // Aggiungi background_image
            $table->string('background_image')->nullable();
            
            // Aggiungi html_template
            $table->longText('html_template')->nullable();
        });
        
        // Rinomina le colonne esistenti
        Schema::table('certificate_templates', function (Blueprint $table) {
            $table->renameColumn('type', 'template_type');
            $table->renameColumn('background_data', 'background_data_old');
        });
        
        // Copia i dati dalla struttura vecchia a quella nuova
        DB::statement("
            UPDATE certificate_templates 
            SET 
                settings = JSON_OBJECT(
                    'background_type', COALESCE(background_type, 'image'),
                    'placeholder_positions', COALESCE(placeholder_positions, JSON_ARRAY()),
                    'styling', COALESCE(styling, JSON_OBJECT())
                ),
                background_image = background_data_old
        ");
        
        // Rimuovi le colonne vecchie
        Schema::table('certificate_templates', function (Blueprint $table) {
            $table->dropColumn([
                'background_data_old',
                'placeholder_positions',
                'styling',
                'background_type',
                'is_default',
                'sort_order'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificate_templates', function (Blueprint $table) {
            // Ripristina le colonne vecchie
            $table->string('type')->default('course');
            $table->string('background_data')->nullable();
            $table->json('placeholder_positions')->nullable();
            $table->json('styling')->nullable();
            $table->string('background_type')->nullable();
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->nullable();
        });
        
        // Copia i dati dalla struttura nuova a quella vecchia
        DB::statement("
            UPDATE certificate_templates 
            SET 
                type = template_type,
                background_data = background_image,
                placeholder_positions = JSON_EXTRACT(settings, '$.placeholder_positions'),
                styling = JSON_EXTRACT(settings, '$.styling'),
                background_type = JSON_UNQUOTE(JSON_EXTRACT(settings, '$.background_type'))
        ");
        
        // Rimuovi le colonne nuove
        Schema::table('certificate_templates', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn([
                'course_id',
                'settings',
                'background_image',
                'html_template',
                'template_type'
            ]);
        });
    }
};