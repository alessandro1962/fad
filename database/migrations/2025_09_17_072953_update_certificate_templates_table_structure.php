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
            // Rename existing columns to match new structure
            $table->renameColumn('template_type', 'type');
            $table->renameColumn('html_template', 'background_data');
            $table->renameColumn('background_image', 'background_type');
            
            // Add new columns
            $table->json('placeholder_positions')->nullable()->after('background_data');
            $table->json('styling')->nullable()->after('placeholder_positions');
            $table->boolean('is_default')->default(false)->after('styling');
            $table->integer('sort_order')->default(0)->after('is_default');
            
            // Remove old columns that are no longer needed
            $table->dropColumn('settings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificate_templates', function (Blueprint $table) {
            // Revert column names
            $table->renameColumn('type', 'template_type');
            $table->renameColumn('background_data', 'html_template');
            $table->renameColumn('background_type', 'background_image');
            
            // Remove new columns
            $table->dropColumn(['placeholder_positions', 'styling', 'is_default', 'sort_order']);
            
            // Add back old columns
            $table->json('settings')->nullable();
        });
    }
};
