<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Branches extends Model
{
    use Notifiable, HasFactory;

    protected $fillable = [
        'name',
        'address',
    ];


        public function users()
    {
        return $this->hasMany(User::class, 'branch_id', 'branch_id');
    }

    public function plans()
    {
        return $this->hasMany(MembershipPlan::class, 'branch_id', 'branch_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscriptions::class, 'branch_id', 'branch_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'branch_id', 'branch_id');
    }

    public function sales()
    {
        return $this->hasMany(Sales::class, 'branch_id', 'branch_id');
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'branch_id', 'branch_id');
    }

    public function logs()
    {
        return $this->hasMany(Logs::class, 'branch_id', 'branch_id');
    }

        public function products()
    {
        return $this->hasMany(Product::class, 'branch_id', 'branch_id');
    }
        public function transaction()
    {
        return $this->hasMany(Transactions::class, 'branch_id', 'branch_id');
    }


}
