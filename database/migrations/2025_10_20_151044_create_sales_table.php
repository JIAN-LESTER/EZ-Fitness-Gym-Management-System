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
        Schema::create('sales', function (Blueprint $table) {
            $table->id('sales_id');
            $table->foreignId('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_method', ['cash', 'credit_card', 'gcash']);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->enum('type', ['memberships', 'products']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
