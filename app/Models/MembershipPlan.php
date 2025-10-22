<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class MembershipPlan extends Model
{
    use HasFactory, Notifiable;


    protected $primaryKey = 'plan_id';
    protected $fillable = [
        'name',
        'price',
        'duration_days',
    ];

    public function member()
    {
        return $this->hasOne(MemberProfile::class, 'plan_id', 'plan_id');
    }
}
