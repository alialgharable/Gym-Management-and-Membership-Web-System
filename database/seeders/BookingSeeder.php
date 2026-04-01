<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Booking;
use App\Models\Member;
use App\Models\GymClass;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = Member::all();
        $classes = GymClass::all();

        foreach ($members as $member) {
            Booking::create([
                'member_id' => $member->id,
                'class_id' => $classes->random()->id,
                'status' => fake()->randomElement(['confirmed', 'pending', 'cancelled']),
            ]);
        }
    }
}
