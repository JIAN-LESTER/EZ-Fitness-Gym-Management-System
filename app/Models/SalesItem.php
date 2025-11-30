<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesItem extends Model
{
    use HasFactory;

    protected $table = 'sales_items';
    protected $primaryKey = 'sales_item_id';

    protected $fillable = [
        'sales_id',
        'product_id',
        'plan_id',
        'quantity',
        'price',
        'sub_total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'sub_total' => 'decimal:2',
    ];

    // Relationship to Sales
    public function sale()
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'sales_id');
    }

    // Relationship to Product (nullable)
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    // Relationship to Membership Plan (nullable)
    public function plan()
    {
        return $this->belongsTo(MembershipPlan::class, 'plan_id', 'plan_id');
    }

    // Helper method to determine item type
    public function getItemTypeAttribute()
    {
        if ($this->product_id) {
            return 'product';
        } elseif ($this->plan_id) {
            return 'membership_plan';
        }
        return 'unknown';
    }

    // Helper method to get item name
    public function getItemNameAttribute()
    {
        if ($this->product) {
            return $this->product->name;
        } elseif ($this->plan) {
            return $this->plan->name;
        }
        return 'Unknown Item';
    }
}