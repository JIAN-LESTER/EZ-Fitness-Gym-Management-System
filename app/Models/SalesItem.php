<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesItem extends Model
{
    use HasFactory;

    // Explicitly set table name
    protected $table = 'sales_items';

    protected $primaryKey = 'sales_item_id';

    // Disable timestamps if your table doesn't have created_at/updated_at
    // public $timestamps = false;

    protected $fillable = [
        'sales_id',
        'product_id',
        'plan_id',
        'subscription_id',
        'quantity',
        'price',
        'sub_total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'sub_total' => 'decimal:2',
    ];

    /**
     * Relationship to Sales
     */
    public function sale()
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'sales_id');
    }

    /**
     * Relationship to Product (nullable)
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Relationship to Membership Plan (nullable)
     */
    public function plan()
    {
        return $this->belongsTo(MembershipPlan::class, 'plan_id', 'plan_id');
    }

    /**
     * Relationship to Subscription (nullable)
     */
    public function subscription()
    {
        return $this->belongsTo(Subscriptions::class, 'subscription_id', 'subscription_id');
    }

    /**
     * Helper method to determine item type
     */
    public function getItemTypeAttribute()
    {
        if ($this->product_id) {
            return 'product';
        } elseif ($this->plan_id) {
            return 'membership_plan';
        } elseif ($this->subscription_id) {
            return 'subscription';
        }

        return 'unknown';
    }

    /**
     * Helper method to get item name
     * FIXED: Typo in 'subscription' (was 'susbcription')
     */
    public function getItemNameAttribute()
    {
        if ($this->product) {
            return $this->product->name;
        } elseif ($this->plan) {
            return $this->plan->name;
        } elseif ($this->subscription) {  // FIXED: was 'susbcription'
            return $this->subscription->name;
        }

        return 'Unknown Item';
    }

    /**
     * Helper method to get item description
     */
    public function getItemDescriptionAttribute()
    {
        if ($this->product) {
            return $this->product->description;
        } elseif ($this->plan) {
            return $this->plan->details;
        } elseif ($this->subscription) {
            return $this->subscription->details;
        }

        return '';
    }

    /**
     * Check if this is a product
     */
    public function isProduct()
    {
        return ! is_null($this->product_id);
    }

    /**
     * Check if this is a membership plan
     */
    public function isPlan()
    {
        return ! is_null($this->plan_id);
    }

    /**
     * Check if this is a subscription
     */
    public function isSubscription()
    {
        return ! is_null($this->subscription_id);
    }
}
