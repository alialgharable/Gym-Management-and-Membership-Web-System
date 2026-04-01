<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        // Create admins from the first 2 users
        foreach ($users->take(2) as $user) {
            Admin::create([
                'user_id' => $user->id,
            ]);
        }
    }
}
