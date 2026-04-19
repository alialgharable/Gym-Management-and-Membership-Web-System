<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use App\Models\Member;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Subscription::with(['member.user', 'plan']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('member.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('plan', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('plan_id')) {
            $query->where('membership_plan_id', $request->input('plan_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $subscriptions = $query->latest()->get();

        return view('subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $plans = MembershipPlan::all();

        return view('subscriptions.create', compact('users', 'plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:membership_plans,id',
        ]);

        if (auth()->user()->isAdmin() || auth()->user()->isTrainer()) {
            abort(403);
        }

        $user = auth()->user();

        // Create member if doesn't exist
        $member = $user->member;
        if (!$member) {
            $member = Member::create([
                'user_id' => $user->id,
            ]);
            // Update user role to member
            $user->update(['role' => 'member']);
        }

        $alreadySubscribed = Subscription::where('member_id', $member->id)
            ->where('status', 'active')
            ->exists();

        $alreadyMessage = 'You already have an active subscription. Cancel it in your profile to subscribe to another plan.';

        if ($alreadySubscribed) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $alreadyMessage], 422);
            }

            return back()->with('error', $alreadyMessage);
        }

        $subscription = Subscription::create([
            'member_id' => $member->id,
            'membership_plan_id' => $request->plan_id,
            'start_date' => now(),
            'end_date' => now()->addMonth(), // later: dynamic based on plan
            'status' => 'active',
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Subscription activated!', 'data' => ['subscription_id' => $subscription->id]], 201);
        }

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
