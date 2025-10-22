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
        Schema::create('stock_outs', function (Blueprint $table) {
             $table->id('stock_out_id');
            $table->foreignId('product_id')->references('product_id')->on('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamp('date');
            $table->enum('reason', ['sale', 'expired', 'damage', 'theft', 'other']);
            $table->foreignId('related_sale_id')->nullable()->references('sales_id')->on('sales')->onDelete('set null')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_outs');
    }
};
