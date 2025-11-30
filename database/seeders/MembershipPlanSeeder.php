<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MembershipPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $membershipPlans = [
            [
                "name" => "Walk-in",
                "details" => "Gym Membership for 1 day",
                "price" => 100,
                "duration_days" => 1,
            ],
            [
                "name" => "1st Time Membership",
                "details" => "Gym Membership for first timers",
                "price" => 1000,
                "duration_days" => 365,
            ],
            [
                "name" => "Monthly Membership",
                "details" => "Gym Membership for a month only",
                "price" => 500,
                "duration_days" => 30,
            ],
            [
                "name" => "1 Year Membership",
                "details" => "Membership for the whole year",
                "price" => 500,
                "duration_days" => 365,
            ],
        ];

        DB::table('membership_plans')->insert($membershipPlans);
    }
}
