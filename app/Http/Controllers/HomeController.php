<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request): RedirectResponse|\Illuminate\View\View
    {
        // If user is authenticated, redirect to their dashboard
        if ($request->user()) {
            if ($request->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if ($request->user()->isTrainer()) {
                return redirect()->route('trainer.dashboard');
            }

            if ($request->user()->isMember()) {
                return redirect()->route('member.dashboard');
            }
        }

        // Show home page for guests
        return view('home');
    }
}
