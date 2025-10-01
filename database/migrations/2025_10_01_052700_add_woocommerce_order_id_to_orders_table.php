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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('woocommerce_order_id')->nullable()->after('id');
            $table->json('meta_data')->nullable()->after('paid_at');
            
            // Index for faster lookups
            $table->index('woocommerce_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['woocommerce_order_id']);
            $table->dropColumn(['woocommerce_order_id', 'meta_data']);
        });
    }
};