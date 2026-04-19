<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MembershipPlanController extends Controller
{
    public function index(Request $request): JsonResponse
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

        $data = $plans->map(function (MembershipPlan $plan) {
            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => (float) $plan->price,
                'duration' => $plan->duration,
                'duration_label' => $plan->durationLabel(),
                'description' => $plan->description,
                'subscriptions_count' => $plan->subscriptions_count ?? 0,
            ];
        });

        return response()->json([
            'message' => 'Plans retrieved successfully',
            'data' => $data,
        ]);
    }

    public function show(MembershipPlan $plan): JsonResponse
    {
        $plan->load('subscriptions.member.user');

        $active = $plan->subscriptions->where('status', 'active')->count();
        $total = $plan->subscriptions->count();

        $subscriptions = $plan->subscriptions->map(function ($s) {
            return [
                'id' => $s->id,
                'status' => $s->status,
                'member_id' => $s->member_id,
                'member_name' => $s->member?->user?->name ?? null,
            ];
        });

        $data = [
            'id' => $plan->id,
            'name' => $plan->name,
            'price' => (float) $plan->price,
            'duration' => $plan->duration,
            'duration_label' => $plan->durationLabel(),
            'description' => $plan->description,
            'active_subscriptions' => $active,
            'total_subscriptions' => $total,
            'subscriptions' => $subscriptions,
        ];

        return response()->json([
            'message' => 'Plan retrieved successfully',
            'data' => $data,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|in:1,3,6,12',
        ]);

        $validated['duration'] = $validated['duration_months'] * 30;
        unset($validated['duration_months']);

        $plan = MembershipPlan::create($validated);

        $data = [
            'id' => $plan->id,
            'name' => $plan->name,
            'price' => (float) $plan->price,
            'duration' => $plan->duration,
            'duration_label' => $plan->durationLabel(),
            'description' => $plan->description,
        ];

        return response()->json([
            'message' => 'Membership plan created successfully',
            'data' => $data,
        ], 201);
    }

    public function update(Request $request, MembershipPlan $plan): JsonResponse
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

        $data = [
            'id' => $plan->id,
            'name' => $plan->name,
            'price' => (float) $plan->price,
            'duration' => $plan->duration,
            'duration_label' => $plan->durationLabel(),
            'description' => $plan->description,
        ];

        return response()->json([
            'message' => 'Membership plan updated successfully',
            'data' => $data,
        ]);
    }

    public function destroy(MembershipPlan $plan): JsonResponse
    {
        $plan->delete();

        return response()->json([
            'message' => 'Membership plan deleted successfully',
        ]);
    }
}
