<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public $timestamps = false;

    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->foreignId('sales_id')->references('sales_id')->on('sales')->onDelete('cascade');
            $table->decimal('amount_paid', 10, 2);
            $table->enum('payment_method', ['cash', 'credit_card', 'qr']);
            $table->string('qr_code')->nullable();
            $table->timestamp('transaction_date');
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
