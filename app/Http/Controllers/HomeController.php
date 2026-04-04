<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\MembershipPlan;

class HomeController extends Controller
{
    public function index()
    {

        $plans = MembershipPlan::all();

        if (auth()->check()) {
            if (auth()->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if (auth()->user()->isTrainer()) {
                return redirect()->route('trainer.dashboard');
            }

            if (auth()->user()->isMember()) {
                return redirect()->route('member.dashboard');
            }
        }

        return view('home', compact('plans'));
    }
}
