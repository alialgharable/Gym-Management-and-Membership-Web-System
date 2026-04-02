@extends('layouts.app')

@section('title', 'Create Review')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Create New Review</h1>
            <p class="section-subtitle">Write a review for a trainer</p>
        </div>
        <a href="{{ route('reviews.index') }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('reviews.store') }}" method="POST">
            @csrf

            <div class="field-group">
                <label class="field-label">Trainer <span style="color: #ff5555;">*</span></label>
                <select name="trainer_id" class="field-select" required>
                    <option value="">Select a trainer...</option>
                    @foreach ($trainers as $trainer)
                        <option value="{{ $trainer->id }}" @selected(old('trainer_id') == $trainer->id)>
                            {{ $trainer->user->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
                @error('trainer_id')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Member <span style="color: #ff5555;">*</span></label>
                <select name="member_id" class="field-select" required>
                    <option value="">Select a member...</option>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>
                            {{ $member->user->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
                @error('member_id')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Rating (1-5) <span style="color: #ff5555;">*</span></label>
                <input type="number" name="rating" class="field-input" value="{{ old('rating') }}" min="1" max="5" required>
                @error('rating')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Comment</label>
                <textarea name="comment" class="field-input" rows="4" style="resize: vertical;" placeholder="Share your experience...">{{ old('comment') }}</textarea>
                @error('comment')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Create Review</button>
                <a href="{{ route('reviews.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
