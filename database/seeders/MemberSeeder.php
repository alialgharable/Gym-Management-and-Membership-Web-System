<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\User;
use App\Models\Trainer;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'member')
            ->whereIn('email', [
                'member1@gymrats.test',
                'member2@gymrats.test',
                'member3@gymrats.test',
                'member4@gymrats.test',
                'member5@gymrats.test',
                'member6@gymrats.test',
                'member7@gymrats.test',
                'member8@gymrats.test',
                'member9@gymrats.test',
                'member10@gymrats.test',
                'member11@gymrats.test',
                'member12@gymrats.test',
                'member13@gymrats.test',
                'member14@gymrats.test',
                'member15@gymrats.test',
            ])
            ->orderBy('id')
            ->get();

        $trainers = Trainer::pluck('id')->values();

        foreach ($users as $index => $user) {
            Member::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'trainer_id' => $trainers->isNotEmpty() ? $trainers[$index % $trainers->count()] : null,
                ]
            );
        }
    }
}
