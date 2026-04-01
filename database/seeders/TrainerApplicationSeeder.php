<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\TrainerApplication;
use App\Models\User;
use App\Models\Admin;

class TrainerApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $admins = Admin::all();

        foreach ($users->take(5) as $user) {
            TrainerApplication::create([
                'user_id' => $user->id,
                'reviewed_by' => $admins->isNotEmpty() ? $admins->random()->id : null,
                'cv_file' => fake()->filePath(),
                'experience' => fake()->paragraph(),
                'certifications' => fake()->optional()->sentence(),
                'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            ]);
        }
    }
}
