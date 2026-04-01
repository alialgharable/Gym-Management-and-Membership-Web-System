<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\GymClass;
use App\Models\Trainer;

class GymClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trainers = Trainer::all();

        foreach ($trainers as $trainer) {
            foreach (range(1, 2) as $i) {
                GymClass::create([
                    'trainer_id' => $trainer->id,
                    'name' => fake()->randomElement([
                        'Yoga Session',
                        'HIIT Workout',
                        'Boxing Class',
                        'Pilates',
                        'CrossFit Training'
                    ]),
                    'description' => fake()->sentence(),
                    'schedule' => fake()->dateTimeBetween('+1 day', '+1 month'),
                    'capacity' => fake()->numberBetween(10, 30),
                ]);
            }
        }
    }
}
