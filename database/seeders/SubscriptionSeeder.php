<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $SubscriptionPlans = [
            [
                "name" => "Walk-in",
                "details" => "Subscription for 1 day",
                "price" => 100,
                "branch_id" => 1,
                "duration_days" => 1,
            ],
            [
                "name" => "1st Time Subscription",
                "details" => "Gym Subscription for first timers",
                "price" => 1000,
                "branch_id" => 1,
                "duration_days" => 365,
            ],
            [
                "name" => "Monthly Subscription",
                "details" => "Gym Subscription for a month only",
                "price" => 500,
                "branch_id" => 1,
                "duration_days" => 30,
            ],
            [
                "name" => "1 Year Subscription",
                "details" => "Subscription for the whole year",
                "price" => 500,
                "branch_id" => 1,
                "duration_days" => 365,
            ],

                     [
                "name" => "Walk-in",
                "details" => "Subscription for 1 day",
                "price" => 100,
                "branch_id" => 2,
                "duration_days" => 1,
            ],
            [
                "name" => "1st Time Subscription",
                "details" => "Gym Subscription for first timers",
                "price" => 1000,
                "branch_id" => 2,
                "duration_days" => 365,
            ],
            [
                "name" => "Monthly Subscription",
                "details" => "Gym Subscription for a month only",
                "price" => 500,
                "branch_id" => 2,
                "duration_days" => 30,
            ],
            [
                "name" => "1 Year Subscription",
                "details" => "Subscription for the whole year",
                "price" => 500,
                "branch_id" => 2,
                "duration_days" => 365,
            ],

                     [
                "name" => "Walk-in",
                "details" => "Subscription for 1 day",
                "price" => 100,
                "branch_id" => 3,
                "duration_days" => 1,
            ],
            [
                "name" => "1st Time Subscription",
                "details" => "Gym Subscription for first timers",
                "price" => 1000,
                "branch_id" => 3,
                "duration_days" => 365,
            ],
            [
                "name" => "Monthly Subscription",
                "details" => "Gym Subscription for a month only",
                "price" => 500,
                "branch_id" => 3,
                "duration_days" => 30,
            ],
            [
                "name" => "1 Year Subscription",
                "details" => "Subscription for the whole year",
                "price" => 500,
                "branch_id" => 3,
                "duration_days" => 365,
            ],

            
        ];

        DB::table('subscriptions')->insert($SubscriptionPlans);
    }
}
