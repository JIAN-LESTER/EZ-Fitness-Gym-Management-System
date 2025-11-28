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
        'sex',
        'birthday',
        'height',
        'weight',
        'mobile_number',
        'qr_code',
        'status',
        'isApproved',
        'isDisabled',
        'approved_at',
        'start_date',
        'end_date',
        'renewal_pending'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function plan() {
        return $this->belongsTo(MembershipPlan::class, 'plan_id', 'plan_id');
    }

    // Fixed relationship
    public function attendances() {
        return $this->hasMany(Attendance::class, 'member_id', 'member_id');
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
        if (!$this->end_date) {
            return false;
        }
        
        $endDate = Carbon::parse($this->end_date);
        return $endDate->isFuture() && $endDate->diffInDays(now()) <= $days;
    }

    /**
     * Get days remaining (negative if expired)
     */
    public function daysRemaining(): int
    {
        if (!$this->end_date) {
            return 0;
        }
        
        return Carbon::parse($this->end_date)->diffInDays(now(), false);
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