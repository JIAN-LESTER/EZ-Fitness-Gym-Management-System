<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperLogs
 */
class Logs extends Model
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'log_id';

    protected $fillable = [
        'user_id',
        'branch_id',
        'action',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branches::class, 'branch_id', 'branch_id');
    }
}
