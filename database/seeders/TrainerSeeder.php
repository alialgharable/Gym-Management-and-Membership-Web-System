<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Trainer;

class TrainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users->take(5) as $user) {
            Trainer::create([
                'user_id' => $user->id,
                'specialty' => fake()->randomElement([
                    'Yoga',
                    'CrossFit',
                    'Boxing',
                    'Cardio',
                    'Strength Training'
                ]),
                'bio' => fake()->paragraph(),
                'profile_image' => null,
            ]);
        }
    }
}
