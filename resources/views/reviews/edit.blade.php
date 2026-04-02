@extends('layouts.app')

@section('title', 'Edit Review')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Edit Review</h1>
            <p class="section-subtitle">Update your review</p>
        </div>
        <a href="{{ route('reviews.show', $review) }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('reviews.update', $review) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field-group">
                <label class="field-label">Trainer</label>
                <input type="text" value="{{ $review->trainer->user->name ?? '' }}" class="field-input" disabled>
            </div>

            <div class="field-group">
                <label class="field-label">Member</label>
                <input type="text" value="{{ $review->member->user->name ?? '' }}" class="field-input" disabled>
            </div>

            <div class="field-group">
                <label class="field-label">Rating (1-5) <span style="color: #ff5555;">*</span></label>
                <input type="number" name="rating" class="field-input" value="{{ old('rating', $review->rating) }}" min="1" max="5" required>
                @error('rating')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Comment</label>
                <textarea name="comment" class="field-input" rows="4" style="resize: vertical;">{{ old('comment', $review->comment) }}</textarea>
                @error('comment')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('reviews.show', $review) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
