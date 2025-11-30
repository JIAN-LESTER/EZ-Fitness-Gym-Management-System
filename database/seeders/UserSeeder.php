<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                "first_name" => "Admin",
                "last_name" => "User",
                "username" => "adminuser",
                "email" => "a3.marbuk@gmail.com",
                "password" => bcrypt("adminuser"),
                "role" => "admin",
                "status" => "active",
                "email_verified_at" => now(),
            ],
            [
                "first_name" => "Staff",
                "last_name" => "User",
                "username" => "staffuser",
                "email" => "garlicpizza15@gmail.com",
                "password" => bcrypt("staffuser"),
                "role" => "staff",
                "status" => "active",
                "email_verified_at" => now(),
            ]

        ];

        DB::table('users')->insert($users);
    }
}
