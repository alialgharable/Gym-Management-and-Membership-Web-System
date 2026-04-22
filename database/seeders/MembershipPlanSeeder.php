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
                'name' => '1 Month',
                'tier' => 'basic',
                'price' => 29.99,
                'duration' => 30,
                'description' => 'Basic one-month membership.',
            ],
            [
                'name' => '3 Months',
                'tier' => 'basic',
                'price' => 79.99,
                'duration' => 90,
                'description' => 'Basic three-month membership.',
            ],
            [
                'name' => '6 Months',
                'tier' => 'basic',
                'price' => 149.99,
                'duration' => 180,
                'description' => 'Basic six-month membership.',
            ],
            [
                'name' => '1 Month',
                'tier' => 'premium',
                'price' => 39.99,
                'duration' => 30,
                'description' => 'Premium one-month membership with trainer request flow.',
            ],
            [
                'name' => '3 Months',
                'tier' => 'premium',
                'price' => 99.99,
                'duration' => 90,
                'description' => 'Premium three-month membership with trainer request flow.',
            ],
            [
                'name' => '6 Months',
                'tier' => 'premium',
                'price' => 179.99,
                'duration' => 180,
                'description' => 'Premium six-month membership with trainer request flow.',
            ],
        ];

        foreach ($plans as $plan) {
            MembershipPlan::updateOrCreate(
                ['tier' => $plan['tier'], 'duration' => $plan['duration']],
                [
                    'name' => $plan['name'],
                    'tier' => $plan['tier'],
                    'price' => $plan['price'],
                    'duration' => $plan['duration'],
                    'description' => $plan['description'],
                ]
            );
        }
    }
}
