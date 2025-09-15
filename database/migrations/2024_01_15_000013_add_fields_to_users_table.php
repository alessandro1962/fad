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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->after('name');
            $table->string('last_name')->after('first_name');
            $table->string('company')->nullable()->after('last_name');
            $table->string('profession')->nullable()->after('company');
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('set null');
            $table->string('provider')->nullable(); // google, microsoft, etc.
            $table->string('provider_id')->nullable();
            $table->boolean('privacy_consent')->default(false);
            $table->boolean('marketing_consent')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->json('preferences')->nullable(); // Preferenze utente
            $table->integer('level')->default(1); // Livello gamification
            $table->integer('experience_points')->default(0);
            
            $table->index(['organization_id']);
            $table->index(['provider', 'provider_id']);
            $table->index(['level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn([
                'first_name', 'last_name', 'company', 'profession',
                'organization_id', 'provider', 'provider_id',
                'privacy_consent', 'marketing_consent', 'last_login_at',
                'preferences', 'level', 'experience_points'
            ]);
        });
    }
};
