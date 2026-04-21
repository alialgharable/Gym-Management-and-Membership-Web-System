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

        $roomMap = [
            'combat' => 'Combat Sports Room',
            'yoga_pilates' => 'Yoga & Pilates Studio',
            'group_training' => 'Group Training Room',
            'fitness_machines' => 'Fitness Machines Hall',
        ];

        $classTemplates = [
            'combat' => [
                'Boxing Fundamentals',
                'MMA Drill Session',
                'Kickboxing Basics',
            ],
            'yoga_pilates' => [
                'Yoga Session',
                'Pilates Flow',
                'Stretch & Mobility',
            ],
            'group_training' => [
                'HIIT Workout',
                'Circuit Blast',
                'Core Conditioning',
            ],
            'fitness_machines' => [
                'Machine Strength',
                'Resistance Training',
                'Weight Room Basics',
            ],
        ];

        if ($rooms->isEmpty()) {
            return;
        }

        foreach ($trainers as $trainer) {
            $category = $trainer->specialty ?: 'group_training';

            if (!isset($classTemplates[$category])) {
                $category = 'group_training';
            }

            $roomName = $roomMap[$category] ?? $roomMap['group_training'];
            $roomId = $rooms->firstWhere('name', $roomName)?->id ?? $rooms->first()?->id;

            $selectedClassNames = collect($classTemplates[$category])
                ->shuffle()
                ->take(2)
                ->values();

            foreach ($selectedClassNames as $className) {
                GymClass::create([
                    'trainer_id' => $trainer->id,
                    'room_id' => $roomId,
                    'name' => $className,
                    'category' => $category,
                    'description' => fake()->sentence(),
                    'schedule' => fake()->dateTimeBetween('+1 day', '+1 month'),
                    'capacity' => fake()->numberBetween(10, 30),
                ]);
            }
        }
    }
}
