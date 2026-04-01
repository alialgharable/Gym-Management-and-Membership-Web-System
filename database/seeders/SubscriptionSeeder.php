<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Subscription;
use App\Models\Member;
use App\Models\MembershipPlan;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = Member::all();
        $plans = MembershipPlan::all();

        foreach ($members as $member) {
            $plan = $plans->random();

            Subscription::create([
                'member_id' => $member->id,
                'membership_plan_id' => $plan->id,
                'start_date' => now(),
                'end_date' => now()->addDays($plan->duration),
                'status' => fake()->randomElement(['active', 'expired']),
            ]);
        }
    }
}
