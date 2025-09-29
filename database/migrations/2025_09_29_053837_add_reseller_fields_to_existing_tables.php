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
        // Add reseller fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_reseller')->default(false);
            $table->foreignId('created_by_reseller_id')->nullable()->constrained('resellers')->onDelete('set null');
        });
        
        // Add reseller fields to organizations table
        Schema::table('organizations', function (Blueprint $table) {
            $table->foreignId('created_by_reseller_id')->nullable()->constrained('resellers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['created_by_reseller_id']);
            $table->dropColumn(['is_reseller', 'created_by_reseller_id']);
        });
        
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['created_by_reseller_id']);
            $table->dropColumn('created_by_reseller_id');
        });
    }
};
