<?php

namespace App\Console\Commands;

use App\Models\MemberProfile;
use Illuminate\Console\Command;
use App\Models\Member;
use Carbon\Carbon;

class CheckMembershipExpirationCommand extends Command
{
    protected $signature = 'membership:check-expiration';
    protected $description = 'Check membership expiration and update statuses';

    public function handle()
    {
        $now = Carbon::now();

        $members = MemberProfile::where('status', 'active')
                         ->where('isApproved', true)
                         ->where('isDisabled', false)
                        //  ->whereNotNull('end_date')
                         ->get();

        foreach ($members as $member) {
            $endDate = Carbon::parse($member->end_date);

            if ($endDate->isPast()) {
                $member->update([
                    'status' => 'expired',
                    'renewal_pending' => true,
                ]);
                $this->info("Member {$member->member_id} expired.");
            }
            elseif ($endDate->diffInDays($now) <= 7 && $endDate->isFuture()) {
                // Optionally, send a notification or email here
                $this->info("Member {$member->member_id} expires in {$endDate->diffInDays($now)} days.");
            }
        }

        return 0;
    }
}
