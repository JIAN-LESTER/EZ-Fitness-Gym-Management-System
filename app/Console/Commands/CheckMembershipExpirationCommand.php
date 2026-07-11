<?php

namespace App\Console\Commands;

use App\Models\Logs;
use App\Models\MemberProfile;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckMembershipExpirationCommand extends Command
{
    protected $signature = 'membership:check-expiration';

    protected $description = 'Check membership expiration and update statuses';

    public function handle()
    {
        $now = Carbon::now();

        // Get active members (NOT suspended or cancelled)
        $members = MemberProfile::where('status', 'active')
            ->where('subscription_status', 'active') // ADDED: Only check active subscriptions
            ->where('isApproved', true)
            ->where('isDisabled', false)
            ->whereNotNull('end_date')
            ->whereNotNull('end_date_for_subscription')
            ->get();

        $expiredCount = 0;
        $expiringSoonCount = 0;

        foreach ($members as $member) {
            // Check subscription end date (more important)
            $subscriptionEndDate = Carbon::parse($member->end_date_for_subscription);
            $planEndDate = Carbon::parse($member->end_date);

            // Use the earlier of the two dates
            $relevantEndDate = $subscriptionEndDate->lt($planEndDate) ? $subscriptionEndDate : $planEndDate;

            if ($relevantEndDate->isPast()) {
                // Membership has expired
                $member->update([
                    'status' => 'expired',
                    'subscription_status' => 'expired',
                    'renewal_pending' => true,
                ]);

                // Log the expiration
                Logs::create([
                    'user_id' => $member->user_id,
                    'branch_id' => $member->user->branch_id ?? null,
                    'action' => "Membership auto-expired for: {$member->user->first_name} {$member->user->last_name}",
                    'timestamp' => now(),
                ]);

                $expiredCount++;
                $this->info("Member {$member->member_id} ({$member->user->first_name} {$member->user->last_name}) expired.");
            } elseif ($relevantEndDate->diffInDays($now) <= 7 && $relevantEndDate->isFuture()) {
                // Membership expiring soon (optional: send notification)
                $daysRemaining = (int) $relevantEndDate->diffInDays($now);
                $expiringSoonCount++;
                $this->info("Member {$member->member_id} ({$member->user->first_name} {$member->user->last_name}) expires in {$daysRemaining} days.");

                // TODO: Send email notification here if needed
            }
        }

        $this->info("\n=== Summary ===");
        $this->info("Expired: {$expiredCount} memberships");
        $this->info("Expiring soon: {$expiringSoonCount} memberships");

        return 0;
    }
}
