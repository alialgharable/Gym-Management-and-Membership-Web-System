<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Trainer;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class TrainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = ['combat', 'yoga_pilates', 'group_training', 'fitness_machines'];

        $trainerUsers = User::where('role', 'trainer')
            ->whereIn('email', [
                'trainer1@gymrats.test',
                'trainer2@gymrats.test',
                'trainer3@gymrats.test',
                'trainer4@gymrats.test',
                'trainer5@gymrats.test',
            ])
            ->orderBy('id')
            ->get();

        $faker = Faker::create();

        foreach ($trainerUsers as $index => $user) {
            Trainer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'specialty' => $specialties[$index % count($specialties)],
                    'bio' => $faker->paragraph(),
                    'salary' => $faker->numberBetween(1200, 2600),
                ]
            );
        }
    }
}
