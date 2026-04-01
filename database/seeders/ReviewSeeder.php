<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\TrainerReview;
use App\Models\Member;
use App\Models\Trainer;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $members = Member::all();
        $trainers = Trainer::all();

        foreach ($members as $member) {
            TrainerReview::create([
                'member_id' => $member->id,
                'trainer_id' => $trainers->random()->id,
                'rating' => fake()->numberBetween(1, 5),
                'comment' => fake()->optional()->sentence(),
            ]);
        }
    }
}
