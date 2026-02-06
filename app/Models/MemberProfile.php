<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperMemberProfile
 */
class MemberProfile extends Model
{
    use HasFactory, Notifiable;

    public $timestamps = false;
    protected $table = 'member_profiles';

    protected $primaryKey = 'member_id';

    protected $fillable = [
        'user_id',
        'plan_id',
        'subscription_id',
        'sex',
        'birthday',
        'height',
        'weight',
        'mobile_number',
        'qr_code',
        'status',
        'subscription_status',

        'isApproved',
        'isApprovedForSubscription',

    
        'isDisabled',
        'isDisabledForSubscription',


        'approved_at',
        'approved_at_for_subscription',

        'start_date',
        'start_date_for_subscription',

        'end_date',
        'end_date_for_subscription',

        'renewal_pending',
        'suspended_at',
        'days_remaining_before_suspend'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function plan()
    {
        return $this->belongsTo(MembershipPlan::class, 'plan_id', 'plan_id');
    }

        public function subscription()
    {
        return $this->belongsTo(Subscriptions::class, 'subscription_id', 'subscription_id');
    }

    // Fixed relationship
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'member_id', 'member_id');
    }

    /**
     * CRITICAL FIX: Check if member is fully approved
     * A member is fully approved when:
     * 1. Both profile AND subscription are approved
     * 2. Subscription status is active
     * 3. Profile status is active
     * 4. Not disabled in any way
     */
    public function isFullyApproved(): bool
    {
        return $this->isApproved == true 
            && $this->isApprovedForSubscription == true
            && $this->subscription_status === 'active'
            && $this->status === 'active'
            && !$this->isDisabled
            && !$this->isDisabledForSubscription;
    }

    /**
     * CRITICAL FIX: Check if profile is incomplete
     * Profile is incomplete ONLY if basic required fields are missing
     * This should NOT return true if the user is fully approved
     */
    public function hasIncompleteProfile(): bool
    {
        // If already fully approved, profile is NOT incomplete
        if ($this->isFullyApproved()) {
            return false;
        }
        
        // Check if basic required fields are missing
        return empty($this->sex) 
            || empty($this->birthday) 
            || empty($this->mobile_number);
    }

    /**
     * Check if member needs approval (has plan & subscription but not approved)
     * This is for members who have submitted everything but admin hasn't approved yet
     */
    public function needsApproval(): bool
    {
        return $this->plan_id 
            && $this->subscription_id 
            && (!$this->isApproved || !$this->isApprovedForSubscription)
            && !$this->isDisabled
            && !$this->isDisabledForSubscription;
    }


    public function isExpired(): bool
    {
        if (!$this->end_date) {
            return false;
        }

        return Carbon::parse($this->end_date)->isPast();
    }

    /**
     * Check if membership is expiring soon (within X days)
     */
    public function isExpiringSoon(int $days = 7): bool
    {
        if (!$this->end_date || $this->isExpired()) {
            return false;
        }

        $endDate = Carbon::parse($this->end_date)->startOfDay();
        $today = Carbon::today();

        return $today->diffInDays($endDate) <= $days;
    }
    
    /**
     * Get days remaining (negative if expired)
     */
    public function daysRemaining(): int
    {
        if (!$this->end_date) {
            return 0;
        }

        return now()->diffInDays(Carbon::parse($this->end_date), false);
    }

    /**
     * Check and update expiration status
     */
    public function checkExpiration(): void
    {
        if ($this->isExpired() && $this->status === 'active') {
            $this->update([
                'status' => 'expired',
                'renewal_pending' => true,
            ]);
        }
    }

    /**
     * Scope: Get all expired memberships
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<=', Carbon::now())
            ->where('status', 'active')
            ->where('isApproved', true)
            ->where('isDisabled', false);
    }

    /**
     * Scope: Get memberships expiring soon
     */
    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->whereBetween('end_date', [
            Carbon::now(),
            Carbon::now()->addDays($days)
        ])
            ->where('status', 'active')
            ->where('isApproved', true)
            ->where('isDisabled', false);
    }
}