<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperMembershipPlan
 */
class MembershipPlan extends Model
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'plan_id';

    protected $fillable = [
        'name',
        'branch_id',
        'details',
        'price',
        'duration_days',

    ];

    public function branch()
    {
        return $this->belongsTo(Branches::class, 'branch_id', 'branch_id');
    }

    public function members()
    {
        return $this->hasMany(MemberProfile::class, 'plan_id', 'plan_id');
    }
}
