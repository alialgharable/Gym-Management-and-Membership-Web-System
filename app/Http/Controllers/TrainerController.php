<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\TrainerReview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrainerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('trainers.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::whereDoesntHave('trainer')->get();
        $specialties = Trainer::SPECIALTIES;

        return view('trainers.create', compact('users', 'specialties'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'specialty' => 'required|in:combat,yoga_pilates,group_training,fitness_machines',
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
        return view('trainers.show', [
            'trainerId' => $trainer->id
        ]);
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit(Trainer $trainer)
    {
        $specialties = Trainer::SPECIALTIES;

        return view('trainers.edit', compact('trainer', 'specialties'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trainer $trainer)
    {
        $validated = $request->validate([
            'specialty' => 'required|in:combat,yoga_pilates,group_training,fitness_machines',
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($trainer->user->profile_picture && Storage::disk('public')->exists($trainer->user->profile_picture)) {
                Storage::disk('public')->delete($trainer->user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $trainer->user->profile_picture = $path;
            $trainer->user->save();
        }

        $trainer->update($validated);

        return redirect()->route('trainers.show', $trainer)
            ->with('success', 'Trainer updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trainer $trainer)
    {
        $user = auth()->user();

        if (!$user || (!$user->isAdmin() && $trainer->user_id !== $user->id)) {
            abort(403);
        }

        $trainerUser = $trainer->user;
        $currentUserId = $user->id;

        $trainer->delete();

        if ($trainerUser && $trainerUser->role === 'trainer') {
            $trainerUser->role = 'user';
            $trainerUser->save();
        }

        if ($currentUserId === $trainerUser?->id) {
            return redirect()->route('home')
                ->with('success', 'Trainer profile deleted successfully.');
        }

        return redirect()->route('trainers.index')
            ->with('success', 'Trainer deleted successfully.');
    }
}
