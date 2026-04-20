<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Trainer;
use Illuminate\Support\Facades\Hash;

class TrainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'trainer@gmail.com'],
            [
                'name' => 'trainer',
                'password' => Hash::make('trainer123'),
                'role' => 'trainer',
            ]
        );

        // Link user to admins table (avoid duplicates)
        Trainer::updateOrCreate([
            'user_id' => $user->id,
            'specialty' => 'combat',
        ]);
    }
}
