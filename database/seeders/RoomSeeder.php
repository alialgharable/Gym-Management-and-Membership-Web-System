<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [
                'name' => 'Combat Sports Room',
                'description' => 'For boxing, MMA, kickboxing, and martial arts classes.',
            ],
            [
                'name' => 'Yoga & Pilates Studio',
                'description' => 'Quiet studio for yoga, pilates, mobility, and stretching sessions.',
            ],
            [
                'name' => 'Group Training Room',
                'description' => 'General-purpose room for group classes and functional training.',
            ],
            [
                'name' => 'Fitness Machines Hall',
                'description' => 'Main floor with fitness machines and strength equipment.',
            ],
        ];

        foreach ($rooms as $room) {
            DB::table('rooms')->updateOrInsert(
                ['name' => $room['name']],
                [
                    'description' => $room['description'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
