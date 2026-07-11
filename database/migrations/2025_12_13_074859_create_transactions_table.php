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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->foreignId('branch_id')->nullable()->references('branch_id')->on('branches')->onDelete('cascade');
            $table->foreignId('sales_id')->nullable()->references('sales_id')->on('sales')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->references('product_id')->on('products')->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->references('plan_id')->on('membership_plans')->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->references('subscription_id')->on('subscriptions')->onDelete('cascade');

            $table->foreignId('performed_by')->nullable()->references('user_id')->on('users')->onDelete('cascade'); // Added this

            $table->enum('type', ['sales', 'stock_in', 'stock_out', 'memberships', 'subscriptions']);
            $table->integer('quantity')->default(0);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
