<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;

class MemberDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user || !$user->isMember()) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $member = Member::with([
            'user',
            'subscription.plan',
            'bookings.gymClass.trainer.user'
        ])->where('user_id', $user->id)->first();

        if (!$member) {
            return response()->json([
                'message' => 'Member profile not found'
            ], 404);
        }

        $activeSub = $member->subscription && $member->subscription->status === 'active'
            ? $member->subscription
            : null;

        $totalBookings = $member->bookings->count();
        $confirmedBookings = $member->bookings->where('status', 'confirmed')->count();

        return response()->json([
            'message' => 'Member dashboard retrieved successfully',
            'data' => [
                'member' => [
                    'id' => $member->id,
                    'created_at' => $member->created_at,
                    'user' => $member->user ? [
                        'id' => $member->user->id,
                        'name' => $member->user->name,
                        'email' => $member->user->email,
                        'profile_picture' => $member->user->profile_picture,
                    ] : null,
                ],
                'stats' => [
                    'total_bookings' => $totalBookings,
                    'confirmed_bookings' => $confirmedBookings,
                ],
                'active_subscription' => $activeSub ? [
                    'id' => $activeSub->id,
                    'status' => $activeSub->status,
                    'start_date' => $activeSub->start_date,
                    'end_date' => $activeSub->end_date,
                    'plan' => $activeSub->plan ? [
                        'id' => $activeSub->plan->id,
                        'name' => $activeSub->plan->name,
                        'price' => $activeSub->plan->price,
                        'duration' => $activeSub->plan->duration,
                    ] : null,
                ] : null,
                'bookings' => $member->bookings->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'status' => $booking->status,
                        'created_at' => $booking->created_at,
                        'gym_class' => $booking->gymClass ? [
                            'id' => $booking->gymClass->id,
                            'name' => $booking->gymClass->name,
                            'schedule' => $booking->gymClass->schedule,
                            'trainer' => $booking->gymClass->trainer ? [
                                'id' => $booking->gymClass->trainer->id,
                                'user' => $booking->gymClass->trainer->user ? [
                                    'id' => $booking->gymClass->trainer->user->id,
                                    'name' => $booking->gymClass->trainer->user->name,
                                ] : null,
                            ] : null,
                        ] : null,
                    ];
                })->values(),
            ]
        ], 200);
    }
}