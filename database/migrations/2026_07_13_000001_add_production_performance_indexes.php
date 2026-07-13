<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('CREATE INDEX IF NOT EXISTS idx_users_lower_email ON users (LOWER(email))');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_users_lower_username ON users (LOWER(username))');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_users_branch_role_status ON users (branch_id, role, status)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_users_created_at ON users (created_at)');

        DB::statement('CREATE INDEX IF NOT EXISTS idx_member_profiles_user_id ON member_profiles (user_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_member_profiles_branch_status ON member_profiles (branch_id, status)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_member_profiles_branch_sub_status ON member_profiles (branch_id, subscription_status)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_member_profiles_branch_start ON member_profiles (branch_id, start_date)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_member_profiles_branch_end ON member_profiles (branch_id, end_date)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_member_profiles_subscription_status ON member_profiles (subscription_id, status)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_member_profiles_plan_id ON member_profiles (plan_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_member_profiles_active_expiry ON member_profiles (end_date_for_subscription, end_date) WHERE status = \'active\' AND subscription_status = \'active\' AND "isApproved" = true AND "isDisabled" = false');

        DB::statement('CREATE INDEX IF NOT EXISTS idx_sales_branch_status_created ON sales (branch_id, status, created_at)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_sales_status_created ON sales (status, created_at)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_sales_branch_created ON sales (branch_id, created_at)');

        DB::statement('CREATE INDEX IF NOT EXISTS idx_sales_items_sales_id ON sales_items (sales_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_sales_items_product_id ON sales_items (product_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_sales_items_plan_id ON sales_items (plan_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_sales_items_subscription_id ON sales_items (subscription_id)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_sales_items_product_sales ON sales_items (product_id, sales_id) WHERE product_id IS NOT NULL AND plan_id IS NULL');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_sales_items_plan_sales ON sales_items (plan_id, sales_id) WHERE plan_id IS NOT NULL');

        DB::statement('CREATE INDEX IF NOT EXISTS idx_attendances_branch_status_checkin ON attendances (branch_id, status, check_in_time)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_attendances_member_checkin ON attendances (member_id, check_in_time DESC)');

        DB::statement('CREATE INDEX IF NOT EXISTS idx_inventories_branch_quantity ON inventories (branch_id, quantity)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_transactions_branch_type_created ON transactions (branch_id, type, created_at)');

        DB::statement('CREATE INDEX IF NOT EXISTS idx_membership_plans_branch_price ON membership_plans (branch_id, price)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_subscriptions_branch_price ON subscriptions (branch_id, price)');

        DB::statement('CREATE INDEX IF NOT EXISTS idx_logs_user_created ON logs (user_id, created_at)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_logs_branch_created ON logs (branch_id, created_at)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS idx_logs_branch_created');
        DB::statement('DROP INDEX IF EXISTS idx_logs_user_created');

        DB::statement('DROP INDEX IF EXISTS idx_subscriptions_branch_price');
        DB::statement('DROP INDEX IF EXISTS idx_membership_plans_branch_price');

        DB::statement('DROP INDEX IF EXISTS idx_transactions_branch_type_created');
        DB::statement('DROP INDEX IF EXISTS idx_inventories_branch_quantity');

        DB::statement('DROP INDEX IF EXISTS idx_attendances_member_checkin');
        DB::statement('DROP INDEX IF EXISTS idx_attendances_branch_status_checkin');

        DB::statement('DROP INDEX IF EXISTS idx_sales_items_plan_sales');
        DB::statement('DROP INDEX IF EXISTS idx_sales_items_product_sales');
        DB::statement('DROP INDEX IF EXISTS idx_sales_items_subscription_id');
        DB::statement('DROP INDEX IF EXISTS idx_sales_items_plan_id');
        DB::statement('DROP INDEX IF EXISTS idx_sales_items_product_id');
        DB::statement('DROP INDEX IF EXISTS idx_sales_items_sales_id');

        DB::statement('DROP INDEX IF EXISTS idx_sales_branch_created');
        DB::statement('DROP INDEX IF EXISTS idx_sales_status_created');
        DB::statement('DROP INDEX IF EXISTS idx_sales_branch_status_created');

        DB::statement('DROP INDEX IF EXISTS idx_member_profiles_active_expiry');
        DB::statement('DROP INDEX IF EXISTS idx_member_profiles_plan_id');
        DB::statement('DROP INDEX IF EXISTS idx_member_profiles_subscription_status');
        DB::statement('DROP INDEX IF EXISTS idx_member_profiles_branch_end');
        DB::statement('DROP INDEX IF EXISTS idx_member_profiles_branch_start');
        DB::statement('DROP INDEX IF EXISTS idx_member_profiles_branch_sub_status');
        DB::statement('DROP INDEX IF EXISTS idx_member_profiles_branch_status');
        DB::statement('DROP INDEX IF EXISTS idx_member_profiles_user_id');

        DB::statement('DROP INDEX IF EXISTS idx_users_created_at');
        DB::statement('DROP INDEX IF EXISTS idx_users_branch_role_status');
        DB::statement('DROP INDEX IF EXISTS idx_users_lower_username');
        DB::statement('DROP INDEX IF EXISTS idx_users_lower_email');
    }
};
