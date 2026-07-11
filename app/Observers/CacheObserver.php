<?php

namespace App\Observers;

use App\Services\CacheService;

class SalesObserver
{
    public function created($model)
    {
        $this->clearCache($model);
    }

    public function updated($model)
    {
        $this->clearCache($model);
    }

    public function deleted($model)
    {
        $this->clearCache($model);
    }

    private function clearCache($model)
    {
        // Clear sales-related cache
        CacheService::forgetPattern('sales_overview');
        CacheService::forgetPattern('monthly_sales_stats');
        CacheService::forgetPattern('sales_trend');
        CacheService::forgetPattern('recent_sales');
        CacheService::forgetPattern('sales_by_type');
        CacheService::forgetPattern('product_performance');
    }
}

class MemberProfileObserver
{
    public function created($model)
    {
        $this->clearCache($model);
    }

    public function updated($model)
    {
        $this->clearCache($model);
    }

    public function deleted($model)
    {
        $this->clearCache($model);
    }

    private function clearCache($model)
    {
        // Clear member-related cache
        CacheService::forgetPattern('member_profile');
        CacheService::forgetPattern('membership_overview');
        CacheService::forgetPattern('membership_trend');
        CacheService::forgetPattern('subscription_stats');
        CacheService::forgetPattern('expiring_memberships');
        CacheService::forgetPattern('plan_performance');
    }
}

class AttendanceObserver
{
    public function created($model)
    {
        $this->clearCache($model);
    }

    public function updated($model)
    {
        $this->clearCache($model);
    }

    public function deleted($model)
    {
        $this->clearCache($model);
    }

    private function clearCache($model)
    {
        // Clear attendance-related cache
        CacheService::forgetPattern('occupancy');
        CacheService::forgetPattern('recent_attendance');
        CacheService::forgetPattern('attendance_counts');
    }
}

class InventoryObserver
{
    public function created($model)
    {
        $this->clearCache($model);
    }

    public function updated($model)
    {
        $this->clearCache($model);
    }

    public function deleted($model)
    {
        $this->clearCache($model);
    }

    private function clearCache($model)
    {
        // Clear inventory-related cache
        CacheService::forgetPattern('inventory_stats');
    }
}

class TransactionObserver
{
    public function created($model)
    {
        $this->clearCache($model);
    }

    public function updated($model)
    {
        $this->clearCache($model);
    }

    public function deleted($model)
    {
        $this->clearCache($model);
    }

    private function clearCache($model)
    {
        // Clear transaction-related cache
        CacheService::forgetPattern('transaction_stats');
    }
}
