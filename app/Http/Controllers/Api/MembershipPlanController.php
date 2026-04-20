<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;

class MembershipPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = MembershipPlan::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = 12;
        $paginator = $query->latest()->paginate($perPage);

        $items = collect($paginator->items())->map(function ($plan) {
            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => $plan->price,
                'duration' => $plan->duration,
                'duration_label' => method_exists($plan, 'durationLabel') ? $plan->durationLabel() : $plan->duration,
                'description' => $plan->description,
                'created_at' => $plan->created_at,
                'updated_at' => $plan->updated_at,
            ];
        })->values();

        return response()->json([
            'message' => 'Plans retrieved successfully',
            'data' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ], 200);
    }

    public function show(MembershipPlan $plan)
    {
        return response()->json([
            'message' => 'Plan retrieved successfully',
            'data' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => $plan->price,
                'duration' => $plan->duration,
                'duration_label' => method_exists($plan, 'durationLabel') ? $plan->durationLabel() : $plan->duration,
                'description' => $plan->description,
                'created_at' => $plan->created_at,
                'updated_at' => $plan->updated_at,
            ],
        ], 200);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $plan = MembershipPlan::create($validated);

        return response()->json([
            'message' => 'Plan created successfully',
            'data' => ['id' => $plan->id],
        ], 201);
    }

    public function update(Request $request, MembershipPlan $plan)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'duration' => 'sometimes|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $plan->update($validated);

        return response()->json([
            'message' => 'Plan updated successfully',
            'data' => ['id' => $plan->id],
        ], 200);
    }

    public function destroy(MembershipPlan $plan)
    {
        $this->authorizeAdmin();

        $plan->delete();

        return response()->json([
            'message' => 'Plan deleted successfully',
        ], 200);
    }

    private function authorizeAdmin()
    {
        $user = auth()->user();

        if (!$user || !$user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }
    }
}