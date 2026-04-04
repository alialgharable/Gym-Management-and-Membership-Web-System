<?php

namespace App\Actions\Fortify;

use App\Models\Member;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:member,trainer'],
            'specialty' => ['required_if:role,trainer', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'role' => $input['role'],
        ]);

        if ($input['role'] === 'member') {
            Member::create(['user_id' => $user->id]);
        }

        if ($input['role'] === 'trainer') {
            Trainer::create([
                'user_id' => $user->id,
                'specialty' => $input['specialty'] ?? 'General',
                'bio' => $input['bio'] ?? null,
            ]);
        }

        return $user;
    }
}
