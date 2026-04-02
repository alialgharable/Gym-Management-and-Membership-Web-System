<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
class MemberDashboardController extends Controller
{
    public function index()
    {
        $member = Member::with(['user', 'subscription', 'bookings.gymClass'])->first();

        if (!$member) {
            return view('member.dashboard', ['member' => null]);
        }

        return view('member.dashboard', compact('member'));
    }
}
