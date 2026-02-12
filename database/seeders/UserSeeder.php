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
                "first_name" => "Super",
                "last_name" => "Admin",
                "username" => "adminuser",
                "email" => "a3.marbuk@gmail.com",
                "password" => bcrypt("adminuser"),
                "role" => "super_admin",
                "status" => "active",
          
                "email_verified_at" => now(),
            ],
            [
                "first_name" => "Admin",
                "last_name" => "User",
                "username" => "adminuser",
                "email" => "a3.marbuk@gmail.com",
                "password" => bcrypt("adminuser"),
                "role" => "admin",
                "status" => "active",
                "branch_id" => 1,
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
                "branch_id" => 1,
                "email_verified_at" => now(),
            ],

              [
                "first_name" => "Admin2",
                "last_name" => "User",
                "username" => "adminuser2",
                "email" => "admin2@gmail.com",
                "password" => bcrypt("adminuser2"),
                "role" => "admin",
                "status" => "active",
                "branch_id" => 2,
                "email_verified_at" => now(),
            ],
            [
                "first_name" => "Staff2",
                "last_name" => "User",
                "username" => "staffuser2",
                "email" => "staff2@gmail.com",
                "password" => bcrypt("staffuser2"),
                "role" => "staff",
                "status" => "active",
                "branch_id" => 2,
                "email_verified_at" => now(),
            ]

        ];

        DB::table('users')->insert($users);
    }
}
