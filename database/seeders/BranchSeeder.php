<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $branches = [
                [
                    "name" => "Main Branch",
                    "address" => "Valencia City, Bukidnon",
                  
                ],
                [
                    "name" => "Secondary Branch",
                    "address" => "Dologon, Maramag, Bukidnon",
                  
                ],
                [
                    "name" => "Tertiary Branch",
                    "address" => "Malaybalay, Bukidnon",
                
                ]
            ];

            DB::table('branches')->insert($branches);
    }
}
