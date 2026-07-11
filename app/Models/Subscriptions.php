<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Subscriptions extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'subscriptions';

    protected $primaryKey = 'subscription_id';

    protected $fillable = [
        'name',
        'branch_id',
        'details',
        'price',
        'duration_days',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
    ];

    /**
     * Get the branch this subscription belongs to
     */
    public function branch()
    {
        return $this->belongsTo(Branches::class, 'branch_id', 'branch_id');
    }

    /**
     * Get all member profiles using this subscription
     * FIXED: Corrected relationship
     */
    public function members()
    {
        return $this->hasMany(MemberProfile::class, 'subscription_id', 'subscription_id');
    }

    /**
     * Scope: Get subscriptions for a specific branch
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope: Get active subscriptions (with at least one active member)
     */
    public function scopeWithActiveMembers($query)
    {
        return $query->has('activeMembers');
    }

    /**
     * Get the formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return '₱'.number_format($this->price, 2);
    }

    /**
     * Get duration in human readable format
     */
    public function getDurationTextAttribute()
    {
        if ($this->duration_days < 30) {
            return $this->duration_days.' day'.($this->duration_days > 1 ? 's' : '');
        } elseif ($this->duration_days < 365) {
            $months = round($this->duration_days / 30);

            return $months.' month'.($months > 1 ? 's' : '');
        } else {
            $years = round($this->duration_days / 365);

            return $years.' year'.($years > 1 ? 's' : '');
        }
    }
}
