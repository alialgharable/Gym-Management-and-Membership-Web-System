<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MembershipPlan;

class MembershipPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 5) as $i) {
            MembershipPlan::create([
                'name' => fake()->randomElement([
                    'Basic Plan',
                    'Gold Plan',
                    'Premium Plan',
                    'Student Plan',
                    'Pro Plan'
                ]),
                'price' => fake()->numberBetween(20, 100),
                'duration' => fake()->numberBetween(30, 90),
                'description' => fake()->sentence(),
            ]);
        }
    }
}
