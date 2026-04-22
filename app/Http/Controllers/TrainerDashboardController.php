<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\PremiumCoachRequest;

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

        $pendingPremiumRequests = collect();

        if ($trainer) {
            $pendingPremiumRequests = PremiumCoachRequest::with(['member.user', 'subscription.plan'])
                ->where('trainer_id', $trainer->id)
                ->where('status', 'pending')
                ->latest()
                ->get();
        }

        return view('trainer.dashboard', compact('trainer', 'pendingPremiumRequests'));
    }
}
