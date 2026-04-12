<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\GymClass;
use App\Models\Member;
use App\Models\MembershipPlan;
use App\Models\Subscription;
use App\Models\Trainer;

class HomeController extends Controller
{
    public function index()
    {

        $plans = MembershipPlan::all();
        $totalClasses = GymClass::count();
        $totalTrainers = Trainer::count();
        $totalMembers = Member::count();
        $totalBookings = Booking::count();

        $upcomingSessions = GymClass::where('schedule', '>=', now())->count();
        $totalUpcomingSeats = (int) GymClass::where('schedule', '>=', now())->sum('capacity');
        $confirmedUpcomingBookings = Booking::where('status', 'confirmed')
            ->whereHas('gymClass', function ($query) {
                $query->where('schedule', '>=', now());
            })
            ->count();

        $openSeats = max($totalUpcomingSeats - $confirmedUpcomingBookings, 0);
        $bookedPercent = $totalUpcomingSeats > 0
            ? (int) min(100, round(($confirmedUpcomingBookings / $totalUpcomingSeats) * 100))
            : 0;

        $activePlans = Subscription::where('status', 'active')
            ->whereDate('end_date', '>=', now()->toDateString())
            ->count();
        $trainersWithClasses = Trainer::whereHas('gymClasses', function ($query) {
            $query->where('schedule', '>=', now());
        })->count();
        $classesToday = GymClass::whereDate('schedule', now()->toDateString())->count();

        return view('home', compact(
            'plans',
            'totalClasses',
            'totalTrainers',
            'totalMembers',
            'totalBookings',
            'upcomingSessions',
            'openSeats',
            'bookedPercent',
            'activePlans',
            'trainersWithClasses',
            'classesToday'
        ));
    }
}
