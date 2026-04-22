<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Subscription;

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
            'bookings.gymClass.trainer.user',
            'programs.trainer.user',
        ])->where('user_id', $user->id)->first();

        if (!$member) {
            return response()->json([
                'message' => 'Member profile not found'
            ], 404);
        }

        $activeSub = Subscription::with('plan')
            ->where('member_id', $member->id)
            ->where('status', 'active')
            ->whereDate('end_date', '>=', now()->toDateString())
            ->latest('end_date')
            ->first();

        $activePlanTier = strtolower((string) ($activeSub?->plan?->tier ?? ''));
        $hasActiveSubscription = (bool) $activeSub;
        $canAccessPrograms = $hasActiveSubscription && $activePlanTier === 'premium';

        $programs = $canAccessPrograms
            ? $member->programs->map(function ($program) {
                return [
                    'id' => $program->id,
                    'title' => $program->title,
                    'duration_weeks' => $program->duration_weeks,
                    'goal' => $program->goal,
                    'notes' => $program->notes,
                    'assigned_coach' => $program->trainer && $program->trainer->user ? [
                        'id' => $program->trainer->id,
                        'name' => $program->trainer->user->name,
                        'email' => $program->trainer->user->email,
                    ] : null,
                ];
            })->values()
            : collect();

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
                'subscription_access' => [
                    'has_active_subscription' => $hasActiveSubscription,
                    'active_plan_tier' => $activePlanTier ?: null,
                    'can_book_classes' => $hasActiveSubscription,
                    'can_access_programs' => $canAccessPrograms,
                ],
                'active_subscription' => $activeSub ? [
                    'id' => $activeSub->id,
                    'status' => $activeSub->status,
                    'start_date' => $activeSub->start_date,
                    'end_date' => $activeSub->end_date,
                    'plan' => $activeSub->plan ? [
                        'id' => $activeSub->plan->id,
                        'name' => $activeSub->plan->name,
                        'tier' => $activeSub->plan->tier,
                        'price' => $activeSub->plan->price,
                        'duration' => $activeSub->plan->duration,
                    ] : null,
                ] : null,
                'programs' => $programs,
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