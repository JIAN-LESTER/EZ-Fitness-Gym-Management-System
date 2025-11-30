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
        Schema::table('member_profiles', function (Blueprint $table) {
          Schema::table('member_profiles', function (Blueprint $table) {
            $table->timestamp('suspended_at')->nullable()->after('approved_at');
            $table->integer('days_remaining_before_suspend')->nullable()->after('suspended_at');
        });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('member_profiles', function (Blueprint $table) {
            $table->dropColumn(['suspended_at', 'days_remaining_before_suspend']);
        });
    }
};
