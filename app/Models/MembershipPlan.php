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
        'details',
        'price',
        'duration_days',

    ];

    public function members()
    {
        return $this->hasMany(MemberProfile::class, 'plan_id', 'plan_id');
    }
}
