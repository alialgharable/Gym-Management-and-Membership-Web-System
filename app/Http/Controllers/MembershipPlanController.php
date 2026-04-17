<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use Illuminate\Http\Request;

class MembershipPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MembershipPlan::withCount('subscriptions');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        if ($request->filled('duration_months')) {
            $months = (int) $request->input('duration_months');
            $days = $months * 30;
            $query->where('duration', $days);
        }

        $plans = $query->latest()->get();

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
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|in:1,3,6,12',
        ]);

        $validated['duration'] = $validated['duration_months'] * 30;
        unset($validated['duration_months']);

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
    public function update(Request $request, MembershipPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'required|string',
            'price' => 'sometimes|numeric|min:0',
            'duration_months' => 'required|integer|in:1,3,6,12',
        ]);

        $validated['duration'] = $validated['duration_months'] * 30;
        unset($validated['duration_months']);

        $plan->update($validated);

        return redirect()->route('plans.show', ['plan' => $plan])
            ->with('success', 'Membership plan updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MembershipPlan $plan)
    {
        $plan->delete();

        return redirect()->route('plans.index')
            ->with('success', 'Membership plan deleted successfully!');
    }
}
