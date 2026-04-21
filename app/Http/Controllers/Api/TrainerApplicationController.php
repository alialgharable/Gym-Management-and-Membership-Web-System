<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use App\Models\TrainerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TrainerApplicationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        $applications = TrainerApplication::with('user')->latest()->get()->map(function ($application) {
            return [
                'id' => $application->id,
                'user_id' => $application->user_id,
                'status' => $application->status,
                'experience' => $application->experience,
                'certifications' => $application->certifications,
                'cv_file' => $application->cv_file,
                'cv_url' => $application->cv_file ? asset('storage/' . $application->cv_file) : null,
                'created_at' => $application->created_at,
                'updated_at' => $application->updated_at,
                'user' => $application->user ? [
                    'id' => $application->user->id,
                    'name' => $application->user->name,
                    'email' => $application->user->email,
                ] : null,
            ];
        })->values();

        return response()->json([
            'message' => 'Trainer applications retrieved successfully',
            'data' => $applications,
        ], 200);
    }

    public function myApplication()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $application = TrainerApplication::with('user')
            ->where('user_id', $user->id)
            ->first();

        if (!$application) {
            return response()->json([
                'message' => 'No trainer application found',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'Trainer application retrieved successfully',
            'data' => $this->formatApplication($application),
        ], 200);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $existingApplication = TrainerApplication::where('user_id', $user->id)->first();

        if ($existingApplication) {
            return response()->json([
                'message' => 'You already submitted a trainer application.',
                'data' => [
                    'id' => $existingApplication->id,
                ],
            ], 422);
        }

        $validated = $request->validate([
            'cv_file' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'experience' => 'required|string',
            'certifications' => 'nullable|string',
        ]);

        $cvPath = $request->file('cv_file')->store('cvs', 'public');

        $application = TrainerApplication::create([
            'user_id' => $user->id,
            'reviewed_by' => null,
            'cv_file' => $cvPath,
            'experience' => $validated['experience'],
            'certifications' => $validated['certifications'] ?? null,
            'status' => 'pending',
        ]);

        $application->load('user');

        return response()->json([
            'message' => 'Application submitted successfully!',
            'data' => $this->formatApplication($application),
        ], 201);
    }

    public function show(TrainerApplication $trainerApplication)
    {
        $user = auth()->user();

        if (!$user || (!$user->isAdmin() && $trainerApplication->user_id !== $user->id)) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        $trainerApplication->load('user');

        return response()->json([
            'message' => 'Trainer application retrieved successfully',
            'data' => $this->formatApplication($trainerApplication),
            'specialties' => Trainer::SPECIALTIES,
        ], 200);
    }

    public function update(Request $request, TrainerApplication $trainerApplication)
    {
        $user = auth()->user();

        if (!$user || $trainerApplication->user_id !== $user->id) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        $validated = $request->validate([
            'experience' => 'required|string',
            'certifications' => 'nullable|string',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($request->hasFile('cv_file')) {
            if ($trainerApplication->cv_file && Storage::disk('public')->exists($trainerApplication->cv_file)) {
                Storage::disk('public')->delete($trainerApplication->cv_file);
            }

            $validated['cv_file'] = $request->file('cv_file')->store('cvs', 'public');
        }

        $trainerApplication->update($validated);
        $trainerApplication->load('user');

        return response()->json([
            'message' => 'Application updated successfully!',
            'data' => $this->formatApplication($trainerApplication),
        ], 200);
    }

    public function accept(Request $request, TrainerApplication $trainerApplication)
    {
        $user = auth()->user();

        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        if ($trainerApplication->status !== 'pending') {
            return response()->json([
                'message' => 'This application has already been reviewed.',
            ], 422);
        }

        if (Trainer::where('user_id', $trainerApplication->user_id)->exists()) {
            return response()->json([
                'message' => 'This user is already a trainer.',
            ], 422);
        }

        $validated = $request->validate([
            'specialty' => ['required', Rule::in(array_keys(Trainer::SPECIALTIES))],
            'bio' => 'nullable|string',
            'salary' => 'required|numeric|min:0|max:999999.99',
        ]);

        $admin = $user->admin;

        if (!$admin) {
            return response()->json([
                'message' => 'Admin record not found for this user.',
            ], 422);
        }

        DB::transaction(function () use ($trainerApplication, $validated, $admin) {
            Trainer::create([
                'user_id' => $trainerApplication->user_id,
                'specialty' => $validated['specialty'],
                'bio' => $validated['bio'] ?? $trainerApplication->experience,
                'salary' => $validated['salary'],
            ]);

            if ($trainerApplication->user) {
                $trainerApplication->user->update([
                    'role' => 'trainer',
                ]);
            }

            $trainerApplication->update([
                'status' => 'approved',
                'reviewed_by' => $admin->id,
            ]);
        });

        $trainerApplication->refresh();
        $trainerApplication->load('user');

        return response()->json([
            'message' => 'Application approved and trainer created successfully!',
            'data' => $this->formatApplication($trainerApplication),
        ], 200);
    }

    public function reject(TrainerApplication $trainerApplication)
    {
        $user = auth()->user();

        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        if ($trainerApplication->status !== 'pending') {
            return response()->json([
                'message' => 'This application has already been reviewed.',
            ], 422);
        }

        $admin = $user->admin;

        if (!$admin) {
            return response()->json([
                'message' => 'Admin record not found for this user.',
            ], 422);
        }

        $trainerApplication->update([
            'status' => 'rejected',
            'reviewed_by' => $admin->id,
        ]);

        $trainerApplication->refresh();
        $trainerApplication->load('user');

        return response()->json([
            'message' => 'Application rejected successfully.',
            'data' => $this->formatApplication($trainerApplication),
        ], 200);
    }

    public function destroy(TrainerApplication $trainerApplication)
    {
        $user = auth()->user();

        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'message' => 'Forbidden',
            ], 403);
        }

        $trainerApplication->delete();

        return response()->json([
            'message' => 'Application deleted successfully',
        ], 200);
    }

    private function formatApplication(TrainerApplication $application)
    {
        return [
            'id' => $application->id,
            'user_id' => $application->user_id,
            'status' => $application->status,
            'experience' => $application->experience,
            'certifications' => $application->certifications,
            'cv_file' => $application->cv_file,
            'cv_url' => $application->cv_file ? asset('storage/' . $application->cv_file) : null,
            'reviewed_by' => $application->reviewed_by,
            'created_at' => $application->created_at,
            'updated_at' => $application->updated_at,
            'user' => $application->user ? [
                'id' => $application->user->id,
                'name' => $application->user->name,
                'email' => $application->user->email,
            ] : null,
        ];
    }
}
