<?php

namespace App\Http\Controllers;

class MemberDashboardController extends Controller
{
    public function index()
    {
        return view('member.dashboard');
    }
}