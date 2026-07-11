<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Enum values are defined in the original create-table migration so this
        // migration remains portable across PostgreSQL and other supported drivers.
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
    }
};
