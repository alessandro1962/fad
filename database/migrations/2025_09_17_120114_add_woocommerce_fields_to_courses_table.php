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
        Schema::table('courses', function (Blueprint $table) {
            // Add only missing WooCommerce integration fields
            $table->json('gallery')->nullable()->after('thumbnail_url');
            $table->integer('sale_price_cents')->nullable()->after('price_cents');
            $table->string('stock_status')->default('instock')->after('sale_price_cents');
            $table->boolean('manage_stock')->default(false)->after('stock_status');
            $table->integer('stock_quantity')->nullable()->after('manage_stock');
            
            // Indexes
            $table->index('stock_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['stock_status']);
            
            $table->dropColumn([
                'gallery',
                'sale_price_cents',
                'stock_status',
                'manage_stock',
                'stock_quantity',
            ]);
        });
    }
};