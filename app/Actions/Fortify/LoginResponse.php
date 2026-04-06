<?php

namespace App\Actions\Fortify;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Admin!');
        }

        if ($user->isTrainer()) {
            return redirect()->route('trainer.dashboard')->with('success', 'Welcome back, Trainer!');
        }

        if ($user->isMember()) {
            return redirect()->route('member.dashboard')->with('success', 'Welcome back!');
        }

        return redirect('/');
    }
}

