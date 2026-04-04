<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Member;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subscriptions = Subscription::with(['member.user', 'plan'])->latest()->get();

        return view('subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $members = Member::with('user')->get();
        $plans = MembershipPlan::all();

        return view('subscriptions.create', compact('members', 'plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        // Optional: prevent duplicate active subscription
        $alreadySubscribed = Subscription::where('user_id', auth()->id())
            ->where('status', 'active')
            ->exists();

        if ($alreadySubscribed) {
            return back()->with('error', 'You already have an active subscription.');
        }

        Subscription::create([
            'user_id' => auth()->id(),
            'plan_id' => $request->plan_id,
            'start_date' => now(),
            'end_date' => now()->addMonth(), // later: dynamic based on plan
            'status' => 'active',
        ]);

        return redirect()->route('home')
            ->with('success', 'Subscription activated!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['member.user', 'plan']);

        return view('subscriptions.show', compact('subscription'));
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit(Subscription $subscription)
    {
        $members = Member::with('user')->get();
        $plans = MembershipPlan::all();

        return view('subscriptions.edit', compact('subscription', 'members', 'plans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'member_id' => 'sometimes|exists:members,id',
            'membership_plan_id' => 'sometimes|exists:membership_plans,id',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'status' => 'sometimes|in:active,inactive,expired',
        ]);

        $subscription->update($validated);

        return redirect()->route('subscriptions.show', $subscription)
            ->with('success', 'Subscription updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->route('subscriptions.index')
            ->with('success', 'Subscription deleted successfully!');
    }
}
