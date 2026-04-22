<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trainerUsers = [
            ['name' => 'Omar Haddad', 'email' => 'trainer1@gymrats.test'],
            ['name' => 'Nour Hamdan', 'email' => 'trainer2@gymrats.test'],
            ['name' => 'Khaled Saad', 'email' => 'trainer3@gymrats.test'],
            ['name' => 'Maya Azar', 'email' => 'trainer4@gymrats.test'],
            ['name' => 'Tariq Mansour', 'email' => 'trainer5@gymrats.test'],
        ];

        $memberUsers = [
            ['name' => 'Ali Yassin', 'email' => 'member1@gymrats.test'],
            ['name' => 'Lina Farah', 'email' => 'member2@gymrats.test'],
            ['name' => 'Rami Nasser', 'email' => 'member3@gymrats.test'],
            ['name' => 'Sara Hamed', 'email' => 'member4@gymrats.test'],
            ['name' => 'Ziad Khalil', 'email' => 'member5@gymrats.test'],
            ['name' => 'Hiba Jaber', 'email' => 'member6@gymrats.test'],
            ['name' => 'Youssef Ali', 'email' => 'member7@gymrats.test'],
            ['name' => 'Dina Mourad', 'email' => 'member8@gymrats.test'],
            ['name' => 'Fadi Elias', 'email' => 'member9@gymrats.test'],
            ['name' => 'Nina Chami', 'email' => 'member10@gymrats.test'],
            ['name' => 'Hassan Ghanem', 'email' => 'member11@gymrats.test'],
            ['name' => 'Rana Nabil', 'email' => 'member12@gymrats.test'],
            ['name' => 'Bassam Younes', 'email' => 'member13@gymrats.test'],
            ['name' => 'Mira Saad', 'email' => 'member14@gymrats.test'],
            ['name' => 'Walid Karam', 'email' => 'member15@gymrats.test'],
        ];

        foreach ($trainerUsers as $trainerUser) {
            User::updateOrCreate(
                ['email' => $trainerUser['email']],
                [
                    'name' => $trainerUser['name'],
                    'password' => Hash::make('Trainer@123'),
                    'role' => 'trainer',
                ]
            );
        }

        foreach ($memberUsers as $memberUser) {
            User::updateOrCreate(
                ['email' => $memberUser['email']],
                [
                    'name' => $memberUser['name'],
                    'password' => Hash::make('Member@123'),
                    'role' => 'member',
                ]
            );
        }
    }
}
