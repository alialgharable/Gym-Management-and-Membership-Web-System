<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use App\Models\Member;
use App\Models\MembershipPlan;
use App\Models\PremiumCoachRequest;
use App\Models\Trainer;
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
            'trainer_id' => 'nullable|exists:trainers,id',
            'member_note' => 'nullable|string|max:1200',
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
            ->whereDate('end_date', '>=', now()->toDateString())
            ->exists();

        $alreadyMessage = 'You already have an active subscription. Cancel it in your profile to subscribe to another plan.';

        if ($alreadySubscribed) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $alreadyMessage], 422);
            }

            return back()->with('error', $alreadyMessage);
        }

        $plan = MembershipPlan::findOrFail((int) $request->plan_id);

        if ($plan->duration < 1) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Invalid plan duration.'], 422);
            }

            return back()->with('error', 'Invalid plan duration.');
        }

        if (strtolower((string) $plan->tier) === 'premium' && !$request->filled('trainer_id')) {
            $msg = 'Please choose a trainer for Premium subscription.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $msg], 422);
            }

            return back()->with('error', $msg);
        }

        if (strtolower((string) $plan->tier) === 'premium') {
            $hasPendingRequest = PremiumCoachRequest::where('member_id', $member->id)
                ->where('status', 'pending')
                ->exists();

            if ($hasPendingRequest) {
                $msg = 'You already have a pending premium coach request.';

                if ($request->expectsJson()) {
                    return response()->json(['message' => $msg], 422);
                }

                return back()->with('error', $msg);
            }
        }

        $subscription = Subscription::create([
            'member_id' => $member->id,
            'membership_plan_id' => $plan->id,
            'start_date' => now(),
            'end_date' => now()->addDays((int) $plan->duration),
            'status' => 'active',
        ]);

        $message = 'Subscription activated!';

        if (strtolower((string) $plan->tier) === 'premium') {
            $trainer = Trainer::findOrFail((int) $request->trainer_id);

            PremiumCoachRequest::create([
                'member_id' => $member->id,
                'trainer_id' => $trainer->id,
                'subscription_id' => $subscription->id,
                'status' => 'pending',
                'member_note' => $request->input('member_note'),
            ]);

            $message = 'Premium subscription activated. Your coach request was sent and is waiting for trainer approval.';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'data' => [
                    'subscription_id' => $subscription->id,
                    'member_id' => $member->id,
                    'start_date' => $subscription->start_date,
                    'end_date' => $subscription->end_date,
                ],
            ], 201);
        }

        return redirect()->route('home')
            ->with('success', $message);
    }

    public function cancelActive(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->isMember()) {
            abort(403);
        }

        $member = $user->member;

        if (!$member) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Member profile not found.'], 404);
            }

            return back()->with('error', 'Member profile not found.');
        }

        $activeSubscription = Subscription::where('member_id', $member->id)
            ->where('status', 'active')
            ->latest('end_date')
            ->first();

        if (!$activeSubscription) {
            $message = 'No active subscription to cancel.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $activeSubscription->update([
            'status' => 'inactive',
            'end_date' => now()->toDateString(),
        ]);

        PremiumCoachRequest::where('member_id', $member->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'trainer_note' => 'Cancelled by member after subscription cancellation.',
                'reviewed_at' => now(),
            ]);

        $message = 'Active subscription cancelled. You can subscribe to another plan now.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 200);
        }

        return back()->with('success', $message);
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
