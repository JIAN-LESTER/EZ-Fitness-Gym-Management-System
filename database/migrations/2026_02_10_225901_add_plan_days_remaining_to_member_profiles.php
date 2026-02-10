<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('member_profiles', function (Blueprint $table) {
            $table->integer('plan_days_remaining_before_suspend')->nullable()->after('days_remaining_before_suspend');
        });
    }

    public function down()
    {
        Schema::table('member_profiles', function (Blueprint $table) {
            $table->dropColumn('plan_days_remaining_before_suspend');
        });
    }
};