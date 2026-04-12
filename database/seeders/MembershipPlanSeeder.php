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
        $plans = [
            [
                'name' => 'Basic Plan',
                'price' => 29.99,
                'duration' => 30,
                'description' => 'Great starter plan for consistent monthly training.',
            ],
            [
                'name' => 'Standard Plan',
                'price' => 79.99,
                'duration' => 90,
                'description' => 'Balanced 3-month plan for building routine and momentum.',
            ],
            [
                'name' => 'Premium Plan',
                'price' => 149.99,
                'duration' => 180,
                'description' => '6-month plan for long-term progress and class consistency.',
            ],
            [
                'name' => 'Annual Plan',
                'price' => 269.99,
                'duration' => 365,
                'description' => 'Best value yearly plan for committed members.',
            ],
        ];

        foreach ($plans as $plan) {
            MembershipPlan::updateOrCreate(
                ['name' => $plan['name']],
                [
                    'price' => $plan['price'],
                    'duration' => $plan['duration'],
                    'description' => $plan['description'],
                ]
            );
        }
    }
}
