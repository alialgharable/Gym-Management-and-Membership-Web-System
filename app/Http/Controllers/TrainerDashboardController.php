<?php

namespace App\Http\Controllers;

use App\Models\Trainer;

class TrainerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user || !$user->isTrainer()) {
            abort(403);
        }

        $trainer = Trainer::with(['user', 'gymClasses.bookings', 'reviews'])
            ->where('user_id', $user->id)
            ->first();

        return view('trainer.dashboard', compact('trainer'));
    }
}
