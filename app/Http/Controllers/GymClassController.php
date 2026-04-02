<?php

namespace App\Http\Controllers;

use App\Models\GymClass;
use App\Models\Trainer;
use Illuminate\Http\Request;

class GymClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = GymClass::with(['trainer.user', 'bookings'])->latest()->get();

        return view('classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $trainers = Trainer::with('user')->get();

        return view('classes.create', compact('trainers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trainer_id' => 'required|exists:trainers,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'schedule' => 'nullable|string',
            'capacity' => 'nullable|integer',
        ]);

        GymClass::create($validated);

        return redirect()->route('classes.index')
            ->with('success', 'Class created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(GymClass $gymClass)
    {
        $gymClass->load(['trainer.user', 'bookings.member.user']);

        return view('classes.show', compact('gymClass'));
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit(GymClass $gymClass)
    {
        $trainers = Trainer::with('user')->get();

        return view('classes.edit', compact('gymClass', 'trainers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GymClass $gymClass)
    {
        $validated = $request->validate([
            'trainer_id' => 'sometimes|exists:trainers,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'schedule' => 'nullable|string',
            'capacity' => 'nullable|integer',
        ]);

        $gymClass->update($validated);

        return redirect()->route('classes.show', $gymClass)
            ->with('success', 'Class updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GymClass $gymClass)
    {
        $gymClass->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Class deleted successfully!');
    }
}
