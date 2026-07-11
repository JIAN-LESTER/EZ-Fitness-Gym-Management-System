<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Transactions extends Model
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'sales_id',
        'product_id',
        'branch_id',
        'plan_id',
        'subscription_id',
        'type',
        'performed_by',
        'quantity',
    ];

    public function sale()
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'sales_id');
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by', 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    // ADD THESE NEW RELATIONSHIPS
    public function plan()
    {
        return $this->belongsTo(MembershipPlan::class, 'plan_id', 'plan_id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscriptions::class, 'subscription_id', 'subscription_id');
    }

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Sales::class,
            'sales_id',
            'user_id',
            'sales_id',
            'user_id'
        );
    }

    public function branch()
    {
        return $this->belongsTo(Branches::class, 'branch_id', 'branch_id');
    }

    public function isProductSale()
    {
        return $this->type === 'sales' && $this->sale && $this->sale->type === 'products';
    }

    public function isMembershipSale()
    {
        return $this->type === 'sales' && $this->sale && $this->sale->type === 'memberships';
    }

    public function isStockMovement()
    {
        return in_array($this->type, ['stock_in', 'stock_out']);
    }
}
