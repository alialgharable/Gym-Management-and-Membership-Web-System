<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\TrainerReview;
use Illuminate\Http\Request;

class TrainerReviewController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->isMember()) {
            return response()->json([
                'message' => 'Only members can submit reviews.'
            ], 403);
        }

        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return response()->json([
                'message' => 'Member profile not found.'
            ], 404);
        }

        $validated = $request->validate([
            'trainer_id' => 'required|exists:trainers,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $existingReview = TrainerReview::where('trainer_id', $validated['trainer_id'])
            ->where('member_id', $member->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'You already reviewed this trainer.'
            ], 422);
        }

        $review = TrainerReview::create([
            'trainer_id' => $validated['trainer_id'],
            'member_id' => $member->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        $review->load('member.user');

        return response()->json([
            'message' => 'Review submitted successfully.',
            'data' => [
                'id' => $review->id,
                'trainer_id' => $review->trainer_id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'member' => $review->member && $review->member->user ? [
                    'id' => $review->member->id,
                    'name' => $review->member->user->name,
                    'email' => $review->member->user->email,
                ] : null,
            ]
        ], 201);
    }

    public function update(Request $request, TrainerReview $review)
    {
        $user = auth()->user();

        if (!$user || !$user->isMember()) {
            return response()->json([
                'message' => 'Only members can update reviews.'
            ], 403);
        }

        $member = Member::where('user_id', $user->id)->first();

        if (!$member || $review->member_id !== $member->id) {
            return response()->json([
                'message' => 'Unauthorized.'
            ], 403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        $review->load('member.user');

        return response()->json([
            'message' => 'Review updated successfully.',
            'data' => [
                'id' => $review->id,
                'trainer_id' => $review->trainer_id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'member' => $review->member && $review->member->user ? [
                    'id' => $review->member->id,
                    'name' => $review->member->user->name,
                    'email' => $review->member->user->email,
                ] : null,
            ]
        ], 200);
    }

    public function destroy(TrainerReview $review)
    {
        $user = auth()->user();

        if (!$user || !$user->isMember()) {
            return response()->json([
                'message' => 'Only members can delete reviews.'
            ], 403);
        }

        $member = Member::where('user_id', $user->id)->first();

        if (!$member || $review->member_id !== $member->id) {
            return response()->json([
                'message' => 'Unauthorized.'
            ], 403);
        }

        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully.'
        ], 200);
    }
}