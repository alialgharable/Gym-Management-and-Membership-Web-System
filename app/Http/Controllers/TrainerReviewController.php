<?php

namespace App\Http\Controllers;

use App\Models\TrainerReview;
use App\Models\Member;
use App\Models\Trainer;
use Illuminate\Http\Request;

class TrainerReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reviews = TrainerReview::with(['member.user', 'trainer.user'])->latest()->get();

        return view('trainer-reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $members = Member::with('user')->get();
        $trainers = Trainer::with('user')->get();

        return view('trainer-reviews.create', compact('members', 'trainers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'trainer_id' => 'required|exists:trainers,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        TrainerReview::create($validated);

        return redirect()->route('trainer-reviews.index')
            ->with('success', 'Review created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TrainerReview $trainerReview)
    {
        $trainerReview->load(['member.user', 'trainer.user']);

        return view('trainer-reviews.show', compact('trainerReview'));
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit(TrainerReview $trainerReview)
    {
        $members = Member::with('user')->get();
        $trainers = Trainer::with('user')->get();

        return view('trainer-reviews.edit', compact('trainerReview', 'members', 'trainers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TrainerReview $trainerReview)
    {
        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $trainerReview->update($validated);

        return redirect()->route('trainer-reviews.show', $trainerReview)
            ->with('success', 'Review updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrainerReview $trainerReview)
    {
        $trainerReview->delete();

        return redirect()->route('trainer-reviews.index')
            ->with('success', 'Review deleted successfully!');
    }
}
