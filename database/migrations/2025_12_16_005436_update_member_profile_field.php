<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Update status ENUM
        DB::statement("
            ALTER TABLE member_profiles 
            MODIFY COLUMN status ENUM(
                'inactive',
                'pending_approval',
                'approved',
                'active',
                'expired',
                'suspended',
                'cancelled'
                'denied'
            ) DEFAULT 'inactive'
        ");

        // Update subscription_status ENUM
        DB::statement("
            ALTER TABLE member_profiles 
            MODIFY COLUMN subscription_status ENUM(
                'inactive',
                'pending_selection',
                'pending_subscription_approval',
                'active',
                'expired',
                'suspended',
                'cancelled'
                'denied'
            ) DEFAULT 'inactive'
        ");

        // Add indexes
        Schema::table('member_profiles', function ($table) {
            $table->index('status');
            $table->index('subscription_status');
        });
    }

    public function down(): void
    {
        Schema::table('member_profiles', function ($table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['subscription_status']);
        });

        DB::statement("
            ALTER TABLE member_profiles 
            MODIFY COLUMN status ENUM('active','expired','suspended','inactive') 
            DEFAULT 'inactive'
        ");

        DB::statement("
            ALTER TABLE member_profiles 
            MODIFY COLUMN subscription_status ENUM('active','expired','suspended','inactive') 
            DEFAULT 'inactive'
        ");
    }
};