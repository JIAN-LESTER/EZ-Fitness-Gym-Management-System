<?php

namespace App\Models;

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
        'start_date',
        'end_date',
    ];

    public function user() {
    return $this->belongsTo(User::class, 'user_id', 'user_id');
}


public function plan() {
    return $this->belongsTo(MembershipPlan::class, 'plan_id', 'plan_id');
}


public function attendance() {
    return $this->hasMany(Attendance::class, 'attendance_id', 'attendance_id');
}

}
