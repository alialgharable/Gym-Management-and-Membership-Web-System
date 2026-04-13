<?php

namespace App\Http\Controllers;

use App\Models\TrainerApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\TrainerController;
use Illuminate\Support\Facades\DB;
use App\Models\Trainer;
class TrainerApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $applications = TrainerApplication::with('user')->latest()->get();

        return view('trainer-applications.index', compact('applications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $application = auth()->user()->trainerApplication;

        if ($application) {
            return redirect()->route('trainer-applications.show', $application);
        }

        return view('trainer-applications.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cv_file' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'experience' => 'required|string',
            'certifications' => 'nullable|string',
        ]);

        $cvPath = $request->file('cv_file')->store('cvs', 'public');

        $application = TrainerApplication::create([
            'user_id' => auth()->id(),
            'reviewed_by' => null,
            'cv_file' => $cvPath,
            'experience' => $validated['experience'],
            'certifications' => $validated['certifications'],
            'status' => 'pending',
        ]);

        return redirect()->route('trainer-applications.show', $application)
            ->with('success', 'Application submitted successfully!');
    }
    /**
     * Display the specified resource.
     */
    public function show(TrainerApplication $trainerApplication)
    {
        if (!auth()->user()->isAdmin() && $trainerApplication->user_id !== auth()->id()) {
            abort(403);
        }
        $trainerApplication->load('user');

        return view('trainer-applications.show', [
            'application' => $trainerApplication,
        ]);
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit(TrainerApplication $trainerApplication)
    {
        if ($trainerApplication->user_id !== auth()->id()) {
            abort(403);
        }

        $trainerApplication->load('user');

        return view('trainer-applications.edit', [
            'application' => $trainerApplication,
        ]);
    }

    public function update(Request $request, TrainerApplication $trainerApplication)
    {
        if ($trainerApplication->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'experience' => 'required|string',
            'certifications' => 'nullable|string',
            'cv_file' => 'nullable|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($request->hasFile('cv_file')) {
            if ($trainerApplication->cv_file && Storage::disk('public')->exists($trainerApplication->cv_file)) {
                Storage::disk('public')->delete($trainerApplication->cv_file);
            }

            $validated['cv_file'] = $request->file('cv_file')->store('cvs', 'public');
        }

        $trainerApplication->update($validated);

        return redirect()->route('trainer-applications.show', $trainerApplication)
            ->with('success', 'Application updated successfully!');
    }


    public function accept(Request $request, TrainerApplication $trainerApplication)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($trainerApplication->status !== 'pending') {
            return back()->with('error', 'This application has already been reviewed.');
        }

        if (Trainer::where('user_id', $trainerApplication->user_id)->exists()) {
            return back()->with('error', 'This user is already a trainer.');
        }

        $validated = $request->validate([
            'specialty' => 'required|in:combat,yoga_pilates,group_training,fitness_machines',
            'bio' => 'nullable|string',
        ]);

        $admin = auth()->user()->admin;

        if (!$admin) {
            return back()->with('error', 'Admin record not found for this user.');
        }

        DB::transaction(function () use ($trainerApplication, $validated, $admin) {
            TrainerController::createTrainer([
                'user_id' => $trainerApplication->user_id,
                'specialty' => $validated['specialty'],
                'bio' => $validated['bio'] ?? $trainerApplication->experience,
            ]);

            $trainerApplication->update([
                'status' => 'approved',
                'reviewed_by' => $admin->id,
            ]);
        });

        return redirect()->route('trainer-applications.index')
            ->with('success', 'Application approved and trainer created successfully!');
    }



    public function reject(TrainerApplication $trainerApplication)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($trainerApplication->status !== 'pending') {
            return back()->with('error', 'This application has already been reviewed.');
        }

        $admin = auth()->user()->admin;

        if (!$admin) {
            return back()->with('error', 'Admin record not found for this user.');
        }

        $trainerApplication->update([
            'status' => 'rejected',
            'reviewed_by' => $admin->id,
        ]);

        return back()->with('success', 'Application rejected successfully.');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrainerApplication $trainerApplication)
    {
        $trainerApplication->delete();

        return redirect()->route('trainer-applications.index')
            ->with('success', 'Application deleted successfully!');
    }
}
