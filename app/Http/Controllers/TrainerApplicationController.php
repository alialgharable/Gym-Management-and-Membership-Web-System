<?php

namespace App\Http\Controllers;

use App\Models\TrainerApplication;
use App\Models\User;
use Illuminate\Http\Request;

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
        $users = User::all();
        return view('trainer-applications.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'cv_file' => 'required|string',
            'experience' => 'required|string',
            'certifications' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        TrainerApplication::create($validated);

        return redirect()->route('trainer-applications.index')
            ->with('success', 'Application submitted successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TrainerApplication $trainerApplication)
    {
        $trainerApplication->load('user');

        return view('trainer-applications.show', compact('trainerApplication'));
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit(TrainerApplication $trainerApplication)
    {
        return view('trainer-applications.edit', compact('trainerApplication'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TrainerApplication $trainerApplication)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,approved,rejected',
        ]);

        $trainerApplication->update($validated);

        return redirect()->route('trainer-applications.show', $trainerApplication)
            ->with('success', 'Application updated successfully!');
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
