<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;



use App\Notifications\CustomResetPassword;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Log;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

        protected $primaryKey = 'user_id'; 
    public $incrementing = true;       
    protected $keyType = 'int';       
    protected $fillable = [
        'user_id',
        'branch_id',
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'role',
        'status',
        'avatar',
        'email_verified_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


        /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail());
    }

        public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    public function member() {
    return $this->hasOne(MemberProfile::class, 'user_id', 'user_id');
}

    public function sales()
    {
        return $this->hasMany(Sales::class, 'user_id', 'user_id');
    }

    public function logs()
    {
        return $this->hasMany(Logs::class, 'user_id', 'user_id');
    }

    public function carts() {
    return $this->hasMany(Cart::class, 'user_id', 'user_id');
}

   public function branch()
    {
        return $this->belongsTo(Branches::class, 'branch_id', 'branch_id');
    }

}
