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
        Schema::create('member_profiles', function (Blueprint $table) {
            $table->id('member_id');
            $table->foreignId('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->references('plan_id')->on('membership_plans')->onDelete('cascade');
            $table->enum('sex', ['male', 'female'])->nullable();
            $table->date('birthday')->nullable();
            $table->float('height')->nullable();
            $table->float('weight')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('qr_code')->unique()->nullable();
            $table->enum('status', ['active', 'expired', 'suspended', 'inactive'])->default('inactive');
            $table->boolean('isApproved')->default(false);
            $table->boolean('isDisabled')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_profiles');
    }
};
