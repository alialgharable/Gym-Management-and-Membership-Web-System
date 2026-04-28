<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use Illuminate\Http\Request;

class MembershipPlanController extends Controller
{
    /**
     * Display the plans page shell. Data is loaded via API.
     */
    public function index(Request $request)
    {
        return view('plans.index');
    }

    /**
     * Show the form for creating a new resource (page shell).
     */
    public function create()
    {
        return view('plans.create');
    }

    /**
     * Display the specified plan page shell.
     */
    public function show(MembershipPlan $plan)
    {
        return view('plans.show', ['planId' => $plan->id]);
    }

    /**
     * Show the form for editing the resource (page shell).
     */
    public function edit(MembershipPlan $plan)
    {
        return view('plans.edit', ['plan' => $plan]);
    }
}
