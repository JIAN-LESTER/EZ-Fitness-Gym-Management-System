<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperAttendance
 */
class Attendance extends Model
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'attendance_id';

    protected $fillable = [
        'member_id',
        'branch_id',
        'check_in_time',
        'check_out_time',
        'status',
        'duration',
    ];

    // Cast to datetime
    protected $casts = [
        'check_in_time' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(MemberProfile::class, 'member_id', 'member_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branches::class, 'branch_id', 'branch_id');
    }
}
