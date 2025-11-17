<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Attendance extends Model
{
    use HasFactory, Notifiable;


    protected $primaryKey = 'attendance_id';
    protected $fillable = [
        'member_id',
        'check_in_time',
        'check_out_time',
        'status',
        'duration,'
    ];

    public function member()
    {
        return $this->belongsTo(MemberProfile::class, 'member_id', 'member_id');
    }
}
