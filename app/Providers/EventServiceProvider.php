<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\Inventory;
use App\Models\MemberProfile;
use App\Models\Sales;
use App\Models\Transactions;
use App\Observers\AttendanceObserver;
use App\Observers\InventoryObserver;
use App\Observers\MemberProfileObserver;
use App\Observers\SalesObserver;
use App\Observers\TransactionObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register observers for automatic cache invalidation
     */
    public function boot()
    {
        // Register model observers for cache invalidation
        Sales::observe(SalesObserver::class);
        MemberProfile::observe(MemberProfileObserver::class);
        Attendance::observe(AttendanceObserver::class);
        Inventory::observe(InventoryObserver::class);
        Transactions::observe(TransactionObserver::class);
    }
}
