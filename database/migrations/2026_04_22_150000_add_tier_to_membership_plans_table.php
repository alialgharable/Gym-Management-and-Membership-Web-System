<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->string('tier')->default('basic')->after('name');
            $table->index(['tier', 'duration']);
        });

        DB::statement("UPDATE membership_plans SET tier = CASE WHEN LOWER(name) LIKE '%premium%' THEN 'premium' ELSE 'basic' END");
        DB::statement("UPDATE membership_plans SET duration = CASE WHEN duration <= 45 THEN 30 WHEN duration <= 135 THEN 90 ELSE 180 END");
        DB::statement("UPDATE membership_plans SET name = CASE WHEN duration = 30 THEN '1 Month' WHEN duration = 90 THEN '3 Months' ELSE '6 Months' END");

        $defaults = [
            ['name' => '1 Month', 'tier' => 'basic', 'duration' => 30, 'price' => 29.99],
            ['name' => '3 Months', 'tier' => 'basic', 'duration' => 90, 'price' => 79.99],
            ['name' => '6 Months', 'tier' => 'basic', 'duration' => 180, 'price' => 149.99],
            ['name' => '1 Month', 'tier' => 'premium', 'duration' => 30, 'price' => 39.99],
            ['name' => '3 Months', 'tier' => 'premium', 'duration' => 90, 'price' => 99.99],
            ['name' => '6 Months', 'tier' => 'premium', 'duration' => 180, 'price' => 179.99],
        ];

        foreach ($defaults as $plan) {
            $exists = DB::table('membership_plans')
                ->where('tier', $plan['tier'])
                ->where('duration', $plan['duration'])
                ->exists();

            if (!$exists) {
                DB::table('membership_plans')->insert([
                    'name' => $plan['name'],
                    'tier' => $plan['tier'],
                    'price' => $plan['price'],
                    'duration' => $plan['duration'],
                    'description' => $plan['tier'] === 'premium'
                        ? 'Premium tier with coach-program request support.'
                        : 'Basic tier membership plan.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $basicPlans = DB::table('membership_plans')->where('tier', 'basic')->get()->keyBy('duration');
        $premiumPlans = DB::table('membership_plans')->where('tier', 'premium')->get();

        foreach ($premiumPlans as $premiumPlan) {
            $basicPlan = $basicPlans->get($premiumPlan->duration);

            if ($basicPlan && (float) $premiumPlan->price <= (float) $basicPlan->price) {
                DB::table('membership_plans')
                    ->where('id', $premiumPlan->id)
                    ->update([
                        'price' => round(((float) $basicPlan->price) * 1.20, 2),
                        'updated_at' => now(),
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropIndex(['tier', 'duration']);
            $table->dropColumn('tier');
        });
    }
};
