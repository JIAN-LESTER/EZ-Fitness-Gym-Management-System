<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('member_profiles', function (Blueprint $table) {
            // Update status to have more options
            $table->enum('status', ['active', 'inactive', 'expired'])->default('inactive')->change();
            
            // Add renewal tracking
            $table->boolean('renewal_pending')->default(false)->after('end_date');
            
            // Add indexes for better performance
            $table->index(['status', 'end_date']);
            $table->index('renewal_pending');
        });
    }

    public function down()
    {
        Schema::table('member_profiles', function (Blueprint $table) {
            $table->dropIndex(['status', 'end_date']);
            $table->dropIndex(['renewal_pending']);
            $table->dropColumn('renewal_pending');
        });
    }
};