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
        Schema::create('reseller_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->constrained()->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->integer('tokens_assigned')->default(0);
            $table->integer('tokens_used')->default(0);
            $table->integer('tokens_purchased')->default(0);
            $table->decimal('price_per_token', 10, 2)->nullable();
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseller_tokens');
    }
};
