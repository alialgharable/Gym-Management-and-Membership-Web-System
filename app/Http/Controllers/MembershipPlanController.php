<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use Illuminate\Http\Request;

class MembershipPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = MembershipPlan::withCount('subscriptions')->latest()->get();

        return view('plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('plans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_months' => 'nullable|integer|min:1',
        ]);

        MembershipPlan::create($validated);

        return redirect()->route('plans.index')
            ->with('success', 'Membership plan created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(MembershipPlan $plan)
    {
        $plan->load('subscriptions.member.user');

        return view('plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit(MembershipPlan $plan)
    {
        return view('plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MembershipPlan $membershipPlan)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'duration_months' => 'nullable|integer|min:1',
        ]);

        $membershipPlan->update($validated);

        return redirect()->route('plans.show', $membershipPlan)
            ->with('success', 'Membership plan updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MembershipPlan $membershipPlan)
    {
        $membershipPlan->delete();

        return redirect()->route('plans.index')
            ->with('success', 'Membership plan deleted successfully!');
    }
}
