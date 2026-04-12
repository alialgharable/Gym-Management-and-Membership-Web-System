<?php

namespace App\Http\Controllers;

use App\Models\GymClass;
use App\Models\Trainer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GymClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user && $user->isTrainer()) {
            $classes = GymClass::with(['trainer.user', 'bookings'])
                ->where('trainer_id', $user->trainer->id)
                ->latest()
                ->get();
        } else {
            $classes = GymClass::with(['trainer.user', 'bookings'])->latest()->get();
        }

        return view('classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        if (!$user || (!$user->isTrainer() && !$user->isAdmin())) {
            abort(403);
        }

        $trainers = Trainer::with('user')->get();
        $isTrainer = $user->isTrainer();

        return view('classes.create', compact('trainers', 'isTrainer'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // If trainer is creating, automatically set their trainer_id
        if ($user && $user->isTrainer()) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'schedule' => 'nullable|date_format:Y-m-d\\TH:i',
                'capacity' => 'nullable|integer|min:1|max:30',
            ]);
            $validated['trainer_id'] = $user->trainer->id;
        } else {
            $validated = $request->validate([
                'trainer_id' => 'required|exists:trainers,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'schedule' => 'nullable|date_format:Y-m-d\\TH:i',
                'capacity' => 'nullable|integer|min:1|max:30',
            ]);
        }

        if (!empty($validated['schedule'])) {
            $validated['schedule'] = Carbon::createFromFormat('Y-m-d\\TH:i', $validated['schedule'])
                ->format('Y-m-d H:i:s');
        }

        GymClass::create($validated);

        return redirect()->route('trainer.dashboard')
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
        $user = auth()->user();

        if (!$user || (!$user->isAdmin() && (!$user->isTrainer() || $gymClass->trainer_id !== $user->trainer->id))) {
            abort(403);
        }

        $trainers = Trainer::with('user')->get();

        return view('classes.edit', compact('gymClass', 'trainers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GymClass $gymClass)
    {
        $user = auth()->user();

        if (!$user || (!$user->isAdmin() && (!$user->isTrainer() || $gymClass->trainer_id !== $user->trainer->id))) {
            abort(403);
        }

        $validated = $request->validate([
            'trainer_id' => 'sometimes|exists:trainers,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'schedule' => 'nullable|date_format:Y-m-d\\TH:i',
            'capacity' => 'nullable|integer|min:1|max:30',
        ]);

        if (!empty($validated['schedule'])) {
            $validated['schedule'] = Carbon::createFromFormat('Y-m-d\\TH:i', $validated['schedule'])
                ->format('Y-m-d H:i:s');
        }

        // Trainers should not reassign class to another trainer
        if ($user->isTrainer()) {
            unset($validated['trainer_id']);
        }

        $gymClass->update($validated);

        return redirect()->route('classes.show', $gymClass)
            ->with('success', 'Class updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GymClass $gymClass)
    {
        $user = auth()->user();

        if (!$user || (!$user->isAdmin() && (!$user->isTrainer() || $gymClass->trainer_id !== $user->trainer->id))) {
            abort(403);
        }

        $gymClass->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Class deleted successfully!');
    }
}
