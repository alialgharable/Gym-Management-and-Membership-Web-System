<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\User;
use Illuminate\Http\Request;

class TrainerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trainers = Trainer::with(['user', 'gymClasses', 'reviews'])->latest()->get();

        return view('trainers.index', compact('trainers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::whereDoesntHave('trainer')->get();

        return view('trainers.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'specialty' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ]);

        Trainer::create($validated);

        return redirect()->route('trainers.index')
            ->with('success', 'Trainer created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Trainer $trainer)
    {
        $trainer->load(['user', 'gymClasses', 'reviews.member.user']);

        return view('trainers.show', compact('trainer'));
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit(Trainer $trainer)
    {
        return view('trainers.edit', compact('trainer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trainer $trainer)
    {
        $validated = $request->validate([
            'specialty' => 'sometimes|string|max:255',
            'bio' => 'nullable|string',
        ]);

        $trainer->update($validated);

        return redirect()->route('trainers.show', $trainer)
            ->with('success', 'Trainer updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trainer $trainer)
    {
        $trainer->delete();

        return redirect()->route('trainers.index')
            ->with('success', 'Trainer deleted successfully!');
    }
}
