<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'category_id' => 1,
                'name' => 'Dumbbell Set',
                'description' => 'Adjustable dumbbell set for strength training.',
                'price' => 75.50,
                'status' => 'available',
                'branch_id' => 1,
                'quantity' => 10,
            ],
            [
                'category_id' => 1,
                'name' => 'Treadmill',
                'description' => 'High-quality treadmill for cardio workouts at home.',
                'price' => 550.00,
                'status' => 'available',
                'branch_id' => 1,
                'quantity' => 5,
            ],
            [
                'category_id' => 2,
                'name' => 'Whey Protein',
                'description' => 'Vanilla-flavored whey protein to support muscle growth.',
                'price' => 49.99,
                'status' => 'available',
                'branch_id' => 1,
                'quantity' => 20,
            ],
            [
                'category_id' => 2,
                'name' => 'Multivitamins',
                'description' => 'Daily multivitamins for overall health and wellness.',
                'price' => 22.50,
                'status' => 'available',
                'branch_id' => 1,
                'quantity' => 30,
            ],
            [
                'category_id' => 3,
                'name' => 'Gym T-Shirt',
                'description' => 'Breathable cotton t-shirt for workouts.',
                'price' => 15.99,
                'status' => 'available',
                'branch_id' => 1,
                'quantity' => 50,
            ],
            [
                'category_id' => 3,
                'name' => 'Workout Shorts',
                'description' => 'Lightweight and comfortable shorts for training.',
                'price' => 18.50,
                'status' => 'available',
                'branch_id' => 1,
                'quantity' => 40,
            ],
            [
                'category_id' => 4,
                'name' => 'Yoga Mat',
                'description' => 'Non-slip yoga mat suitable for all exercises.',
                'price' => 25.99,
                'status' => 'available',
                'branch_id' => 1,
                'quantity' => 25,
            ],
            [
                'category_id' => 4,
                'name' => 'Resistance Bands',
                'description' => 'Set of 5 resistance bands for full-body workouts.',
                'price' => 19.99,
                'status' => 'available',
                'branch_id' => 1,
                'quantity' => 30,
            ],

            [
                'category_id' => 1,
                'name' => 'Dumbbell Set',
                'description' => 'Adjustable dumbbell set for strength training.',
                'price' => 75.50,
                'status' => 'available',
                'branch_id' => 2,
                'quantity' => 10,
            ],
            [
                'category_id' => 1,
                'name' => 'Treadmill',
                'description' => 'High-quality treadmill for cardio workouts at home.',
                'price' => 550.00,
                'status' => 'available',
                'branch_id' => 2,
                'quantity' => 5,
            ],
            [
                'category_id' => 2,
                'name' => 'Whey Protein',
                'description' => 'Vanilla-flavored whey protein to support muscle growth.',
                'price' => 49.99,
                'status' => 'available',
                'branch_id' => 2,
                'quantity' => 20,
            ],
            [
                'category_id' => 2,
                'name' => 'Multivitamins',
                'description' => 'Daily multivitamins for overall health and wellness.',
                'price' => 22.50,
                'status' => 'available',
                'branch_id' => 2,
                'quantity' => 30,
            ],
            [
                'category_id' => 3,
                'name' => 'Gym T-Shirt',
                'description' => 'Breathable cotton t-shirt for workouts.',
                'price' => 15.99,
                'status' => 'available',
                'branch_id' => 2,
                'quantity' => 50,
            ],
            [
                'category_id' => 3,
                'name' => 'Workout Shorts',
                'description' => 'Lightweight and comfortable shorts for training.',
                'price' => 18.50,
                'status' => 'available',
                'branch_id' => 2,
                'quantity' => 40,
            ],
            [
                'category_id' => 4,
                'name' => 'Yoga Mat',
                'description' => 'Non-slip yoga mat suitable for all exercises.',
                'price' => 25.99,
                'status' => 'available',
                'branch_id' => 2,
                'quantity' => 25,
            ],
            [
                'category_id' => 4,
                'name' => 'Resistance Bands',
                'description' => 'Set of 5 resistance bands for full-body workouts.',
                'price' => 19.99,
                'status' => 'available',
                'branch_id' => 2,
                'quantity' => 30,
            ],

            [
                'category_id' => 1,
                'name' => 'Dumbbell Set',
                'description' => 'Adjustable dumbbell set for strength training.',
                'price' => 75.50,
                'status' => 'available',
                'branch_id' => 3,
                'quantity' => 10,
            ],
            [
                'category_id' => 1,
                'name' => 'Treadmill',
                'description' => 'High-quality treadmill for cardio workouts at home.',
                'price' => 550.00,
                'status' => 'available',
                'branch_id' => 3,
                'quantity' => 5,
            ],
            [
                'category_id' => 2,
                'name' => 'Whey Protein',
                'description' => 'Vanilla-flavored whey protein to support muscle growth.',
                'price' => 49.99,
                'status' => 'available',
                'branch_id' => 3,
                'quantity' => 20,
            ],
            [
                'category_id' => 2,
                'name' => 'Multivitamins',
                'description' => 'Daily multivitamins for overall health and wellness.',
                'price' => 22.50,
                'status' => 'available',
                'branch_id' => 3,
                'quantity' => 30,
            ],
            [
                'category_id' => 3,
                'name' => 'Gym T-Shirt',
                'description' => 'Breathable cotton t-shirt for workouts.',
                'price' => 15.99,
                'status' => 'available',
                'branch_id' => 3,
                'quantity' => 50,
            ],
            [
                'category_id' => 3,
                'name' => 'Workout Shorts',
                'description' => 'Lightweight and comfortable shorts for training.',
                'price' => 18.50,
                'status' => 'available',
                'branch_id' => 3,
                'quantity' => 40,
            ],
            [
                'category_id' => 4,
                'name' => 'Yoga Mat',
                'description' => 'Non-slip yoga mat suitable for all exercises.',
                'price' => 25.99,
                'status' => 'available',
                'branch_id' => 3,
                'quantity' => 25,
            ],
            [
                'category_id' => 4,
                'name' => 'Resistance Bands',
                'description' => 'Set of 5 resistance bands for full-body workouts.',
                'price' => 19.99,
                'status' => 'available',
                'branch_id' => 3,
                'quantity' => 30,
            ],
        ];

        foreach ($products as $p) {
            $product = Product::create([
                'category_id' => $p['category_id'],
                'name' => $p['name'],
                'description' => $p['description'],
                'price' => number_format((float) $p['price'], 2, '.', ''),
                'status' => $p['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Inventory::create([
                'product_id' => $product->product_id,
                'quantity' => $p['quantity'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
