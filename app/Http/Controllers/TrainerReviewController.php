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
        $user = auth()->user();

        if ($user && $user->isAdmin()) {
            $reviews = TrainerReview::with(['member.user', 'trainer.user'])->latest()->get();
        } elseif ($user && $user->isMember()) {
            $reviews = TrainerReview::with(['member.user', 'trainer.user'])
                ->whereHas('member', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->latest()
                ->get();
        } elseif ($user && $user->isTrainer() && $user->trainer) {
            $reviews = TrainerReview::with(['member.user', 'trainer.user'])
                ->where('trainer_id', $user->trainer->id)
                ->latest()
                ->get();
        } else {
            $reviews = collect();
        }

        return view('reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->isMember() || !$user->member) {
            abort(403);
        }

        $members = Member::with('user')->get();
        $trainers = Trainer::with('user')->get();
        $selectedTrainerId = $request->integer('trainer_id');

        return view('reviews.create', compact('members', 'trainers', 'selectedTrainerId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->isMember() || !$user->member) {
            abort(403);
        }

        $validated = $request->validate([
            'trainer_id' => 'required|exists:trainers,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $alreadyReviewed = TrainerReview::where('member_id', $user->member->id)
            ->where('trainer_id', $validated['trainer_id'])
            ->exists();

        if ($alreadyReviewed) {
            return back()->withInput()->with('error', 'You already reviewed this trainer. You can edit your existing review.');
        }

        $validated['member_id'] = $user->member->id;

        TrainerReview::create($validated);

        return redirect()->route('trainers.show', $validated['trainer_id'])
            ->with('success', 'Review created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TrainerReview $review)
    {
        $review->load(['member.user', 'trainer.user']);

        $this->authorizeReviewAccess($review);

        return view('reviews.show', ['review' => $review]);
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit(TrainerReview $review)
    {
        $this->authorizeReviewAccess($review, true);

        $members = Member::with('user')->get();
        $trainers = Trainer::with('user')->get();

        return view('reviews.edit', [
            'review' => $review,
            'members' => $members,
            'trainers' => $trainers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TrainerReview $review)
    {
        $this->authorizeReviewAccess($review, true);

        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review->update($validated);

        return redirect()->route('reviews.show', $review)
            ->with('success', 'Review updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrainerReview $review)
    {
        $this->authorizeReviewAccess($review, true);

        $review->delete();

        return redirect()->route('reviews.index')
            ->with('success', 'Review deleted successfully!');
    }

    private function authorizeReviewAccess(TrainerReview $review, bool $requireOwnerOrAdmin = false): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isTrainer() && $user->trainer && $review->trainer_id === $user->trainer->id && !$requireOwnerOrAdmin) {
            return;
        }

        if ($user->isMember() && $review->member && $review->member->user_id === $user->id) {
            return;
        }

        abort(403);
    }
}
