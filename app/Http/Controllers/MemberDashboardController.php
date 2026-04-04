<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
class MemberDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user || !$user->isMember()) {
            abort(403);
        }

        $member = Member::with(['user', 'subscription', 'bookings.gymClass'])
            ->where('user_id', $user->id)
            ->first();

        return view('member.dashboard', compact('member'));
    }
}
