<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\GymClass;
use App\Models\Room;
use App\Models\Trainer;

class GymClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trainers = Trainer::all();
        $rooms = Room::all();

        if ($rooms->isEmpty()) {
            return;
        }

        foreach ($trainers as $trainer) {
            foreach (range(1, 2) as $i) {
                $category = fake()->randomElement([
                    'combat',
                    'yoga_pilates',
                    'group_training',
                    'fitness_machines',
                ]);

                $roomId = match ($category) {
                    'combat' => 1,
                    'yoga_pilates' => 2,
                    'group_training' => 3,
                    'fitness_machines' => 4,
                    default => 3,
                };

                GymClass::create([
                    'trainer_id' => $trainer->id,
                    'room_id' => $rooms->firstWhere('id', $roomId)?->id ?? $rooms->first()->id,
                    'name' => fake()->randomElement([
                        'Yoga Session',
                        'HIIT Workout',
                        'Boxing Class',
                        'Pilates',
                        'CrossFit Training'
                    ]),
                    'category' => $category,
                    'description' => fake()->sentence(),
                    'schedule' => fake()->dateTimeBetween('+1 day', '+1 month'),
                    'capacity' => fake()->numberBetween(10, 30),
                ]);
            }
        }
    }
}
