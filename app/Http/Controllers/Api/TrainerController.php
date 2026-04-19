<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use App\Models\TrainerReview;
use Illuminate\Http\Request;

class TrainerController extends Controller
{
    public function index(Request $request)
    {
        $query = Trainer::with(['user', 'gymClasses', 'reviews.member.user']);

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('specialty')) {
            $query->where('specialty', $request->input('specialty'));
        }

        $trainers = $query->latest()->get()->map(function ($trainer) {
            return [
                'id' => $trainer->id,
                'user_id' => $trainer->user_id,
                'specialty' => $trainer->specialty,
                'specialty_label' => method_exists($trainer, 'specialtyLabel')
                    ? $trainer->specialtyLabel()
                    : $trainer->specialty,
                'bio' => $trainer->bio,
                'created_at' => $trainer->created_at,
                'updated_at' => $trainer->updated_at,

                'user' => $trainer->user ? [
                    'id' => $trainer->user->id,
                    'name' => $trainer->user->name,
                    'email' => $trainer->user->email,
                    'profile_picture' => $trainer->user->profile_picture,
                ] : null,

                'gym_classes' => $trainer->gymClasses->map(function ($gymClass) {
                    return [
                        'id' => $gymClass->id,
                        'name' => $gymClass->name,
                        'schedule' => $gymClass->schedule,
                        'capacity' => $gymClass->capacity,
                    ];
                })->values(),

                'reviews' => $trainer->reviews()->with('member.user')->orderBy('updated_at', 'desc')->get()->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'member' => $review->member && $review->member->user ? [
                            'id' => $review->member->id,
                            'name' => $review->member->user->name,
                        ] : null,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'message' => 'Trainers retrieved successfully',
            'data' => $trainers,
        ], 200);
    }

    public function show(Trainer $trainer)
    {
        $trainer->load(['user', 'gymClasses', 'reviews.member.user']);

        $memberReview = null;

        if (auth()->check() && auth()->user()->isMember()) {
            $memberReview = TrainerReview::where('trainer_id', $trainer->id)
                ->whereHas('member', function ($query) {
                    $query->where('user_id', auth()->id());
                })
                ->first();
        }

        return response()->json([
            'message' => 'Trainer retrieved successfully',
            'data' => [
                'id' => $trainer->id,
                'user_id' => $trainer->user_id,
                'specialty' => $trainer->specialty,
                'specialty_label' => method_exists($trainer, 'specialtyLabel')
                    ? $trainer->specialtyLabel()
                    : $trainer->specialty,
                'bio' => $trainer->bio,
                'created_at' => $trainer->created_at,
                'updated_at' => $trainer->updated_at,

                'user' => $trainer->user ? [
                    'id' => $trainer->user->id,
                    'name' => $trainer->user->name,
                    'email' => $trainer->user->email,
                    'profile_picture' => $trainer->user->profile_picture,
                ] : null,

                'gym_classes' => $trainer->gymClasses->map(function ($gymClass) {
                    return [
                        'id' => $gymClass->id,
                        'name' => $gymClass->name,
                        'schedule' => $gymClass->schedule,
                        'capacity' => $gymClass->capacity,
                    ];
                })->values(),

                'reviews' => $trainer->reviews()->with('member.user')->orderBy('updated_at', 'desc')->get()->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'member' => $review->member && $review->member->user ? [
                            'id' => $review->member->id,
                            'user_id' => $review->member->user->id,
                            'name' => $review->member->user->name,
                            'email' => $review->member->user->email,
                        ] : null,
                    ];
                })->values(),
            ],
            'member_review' => $memberReview ? [
                'id' => $memberReview->id,
                'rating' => $memberReview->rating,
                'comment' => $memberReview->comment,
                'trainer_id' => $memberReview->trainer_id,
            ] : null,
        ], 200);
    }
}