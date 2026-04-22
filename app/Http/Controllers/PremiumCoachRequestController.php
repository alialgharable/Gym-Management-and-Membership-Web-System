<?php

namespace App\Http\Controllers;

use App\Models\PremiumCoachRequest;
use Illuminate\Http\Request;

class PremiumCoachRequestController extends Controller
{
    public function approve(Request $request, PremiumCoachRequest $premiumCoachRequest)
    {
        $user = auth()->user();

        if (!$user || !$user->isTrainer() || !$user->trainer || $premiumCoachRequest->trainer_id !== $user->trainer->id) {
            abort(403);
        }

        if ($premiumCoachRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $premiumCoachRequest->update([
            'status' => 'approved',
            'trainer_note' => $request->input('trainer_note'),
            'reviewed_at' => now(),
        ]);

        $premiumCoachRequest->member->update([
            'trainer_id' => $premiumCoachRequest->trainer_id,
        ]);

        return back()->with('success', 'Premium request approved. You can now create programs for this member.');
    }

    public function reject(Request $request, PremiumCoachRequest $premiumCoachRequest)
    {
        $user = auth()->user();

        if (!$user || !$user->isTrainer() || !$user->trainer || $premiumCoachRequest->trainer_id !== $user->trainer->id) {
            abort(403);
        }

        if ($premiumCoachRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $premiumCoachRequest->update([
            'status' => 'rejected',
            'trainer_note' => $request->input('trainer_note'),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Premium request rejected.');
    }
}
