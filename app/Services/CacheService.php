<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Get cache duration based on data type
     */
    public static function getDuration(string $type): int
    {
        return match ($type) {
            'realtime' => 60,           // 1 minute for real-time data (occupancy, today's stats)
            'hourly' => 3600,           // 1 hour for semi-static data (inventory, products)
            'daily' => 86400,           // 24 hours for static data (plans, subscriptions)
            'stats' => 300,             // 5 minutes for dashboard stats
            default => 1800,            // 30 minutes default
        };
    }

    /**
     * Generate cache key with branch context
     */
    public static function key(string $prefix, ...$params): string
    {
        $branchId = session('selected_branch_id') ?? Auth::user()->branch_id ?? 'all';
        $parts = array_merge([$prefix, $branchId], $params);

        return implode(':', array_filter($parts));
    }

    /**
     * Remember data with automatic key generation
     */
    public static function remember(string $prefix, string $type, callable $callback, ...$params)
    {
        $key = self::key($prefix, ...$params);
        $duration = self::getDuration($type);

        return Cache::remember($key, $duration, $callback);
    }

    /**
     * Forget cache by prefix pattern
     */
    public static function forgetPattern(string $prefix): void
    {
        $branchId = session('selected_branch_id') ?? Auth::user()->branch_id ?? 'all';
        Cache::forget(self::key($prefix));
    }

    /**
     * Flush all branch-related cache
     */
    public static function flushBranch(?int $branchId = null): void
    {
        $branchId = $branchId ?? session('selected_branch_id') ?? Auth::user()->branch_id;

        $patterns = [
            'dashboard', 'stats', 'sales', 'members', 'inventory',
            'occupancy', 'subscriptions', 'attendance',
        ];

        foreach ($patterns as $pattern) {
            Cache::forget(self::key($pattern));
        }
    }
}
