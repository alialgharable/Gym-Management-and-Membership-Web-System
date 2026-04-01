<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
class MemberDashboardController extends Controller
{
    public function index()
    {
        $member = Member::with(['user', 'subscription'])->first();

        return view('member.dashboard', compact('member'));
    }
}
