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
        Schema::create('sales_items', function (Blueprint $table) {
            $table->id('sales_item_id');
            $table->foreignId('sales_id')->references('sales_id')->on('sales')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->references('product_id')->on('products')->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->references('plan_id')->on('membership_plans')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 8, 2);
            $table->decimal('sub_total', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales__items');
    }
};
