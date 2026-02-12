<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MemberProfile;
use App\Models\Logs;
use Carbon\Carbon;

class ExpireMemberships extends Command
{
    protected $signature = 'memberships:expire';
    protected $description = 'Automatically expire memberships that have passed their end date';

    public function handle()
    {
        $now = Carbon::now();
        
        // Find all active memberships that have expired
        $expiredMembers = MemberProfile::where('subscription_status', 'active')
            ->whereNotNull('end_date_for_subscription')
            ->where('end_date_for_subscription', '<', $now)
            ->with('user:user_id,first_name,last_name,branch_id')
            ->get();

        $count = 0;
        
        foreach ($expiredMembers as $member) {
            $member->update([
                'subscription_status' => 'expired',
                'status' => 'expired'
            ]);

            // Log the expiration
            Logs::create([
                'user_id' => $member->user_id,
                'branch_id' => $member->user->branch_id ?? null,
                'action' => "Membership auto-expired for: {$member->user->first_name} {$member->user->last_name}",
                'timestamp' => now(),
            ]);

            $count++;
        }

        $this->info("Expired {$count} memberships.");
        
        return 0;
    }
}