<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->foreignId('branch_id')->nullable()->references('branch_id')->on('branches')->onDelete('cascade');
            $table->foreignId('category_id')->references('category_id')->on('categories')->onDelete('cascade');
            $table->string('name');
            $table->text('image')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
