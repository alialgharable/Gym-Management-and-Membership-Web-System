<?php

namespace App\Http\Controllers;

use App\Models\Trainer;

class TrainerDashboardController extends Controller
{
    public function index()
    {
        $trainer = Trainer::with(['user', 'gymClasses.bookings', 'reviews'])->first();

        if (!$trainer) {
            return view('trainer.dashboard', ['trainer' => null]);
        }

        return view('trainer.dashboard', compact('trainer'));
    }
}
