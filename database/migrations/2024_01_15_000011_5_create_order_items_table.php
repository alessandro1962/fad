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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('item_type'); // course, track, full_vision
            $table->unsignedBigInteger('item_id'); // ID del corso, track, etc.
            $table->integer('quantity')->default(1);
            $table->integer('unit_price_cents');
            $table->integer('total_cents');
            $table->timestamps();
            
            $table->index(['order_id', 'item_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
