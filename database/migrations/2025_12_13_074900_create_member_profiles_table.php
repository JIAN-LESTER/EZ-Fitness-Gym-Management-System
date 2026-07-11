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
        Schema::create('member_profiles', function (Blueprint $table) {
            $table->id('member_id');
            $table->foreignId('branch_id')->nullable()->references('branch_id')->on('branches')->onDelete('cascade');
            $table->foreignId('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->references('plan_id')->on('membership_plans')->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->references('subscription_id')->on('subscriptions')->onDelete('cascade');
            $table->enum('sex', ['male', 'female', 'others'])->nullable();
            $table->date('birthday')->nullable();
            $table->float('height')->nullable();
            $table->float('weight')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('qr_code')->unique()->nullable();

            $table->enum('status', [
                'inactive', 'pending_approval', 'approved', 'active', 'expired',
                'suspended', 'cancelled', 'denied',
            ])->default('inactive');
            $table->enum('subscription_status', [
                'inactive', 'pending_selection', 'pending_subscription_approval',
                'active', 'expired', 'suspended', 'cancelled', 'denied',
            ])->default('inactive');

            $table->boolean('renewal_pending')->default(false);
            $table->timestamp('suspended_at')->nullable();
            $table->integer('days_remaining_before_suspend')->nullable();

            $table->boolean('isApproved')->default(false);
            $table->boolean('isApprovedForSubscription')->default(false);

            $table->boolean('isDisabled')->default(false);
            $table->boolean('isDisabledForSubscription')->default(false);

            $table->timestamp('approved_at')->nullable();

            $table->timestamp('approved_at_for_subscription')->nullable();

            $table->timestamp('start_date')->nullable();
            $table->timestamp('start_date_for_subscription')->nullable();

            $table->timestamp('end_date')->nullable();
            $table->timestamp('end_date_for_subscription')->nullable();

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
