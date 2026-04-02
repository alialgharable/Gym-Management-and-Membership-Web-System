@extends('layouts.app')

@section('title', 'Review Details')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Review for {{ $review->trainer->user->name ?? 'Trainer' }}</h1>
            <p class="section-subtitle">Rating: <span style="color: #ffd700;">★ {{ $review->rating }}/5</span></p>
        </div>
        <div class="actions">
            <a href="{{ route('reviews.index') }}" class="btn btn-secondary">← Back</a>
            <a href="{{ route('reviews.edit', $review) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: 1fr 1fr;">
        <div class="card">
            <h3>Trainer</h3>
            <p><strong>Name:</strong> {{ $review->trainer->user->name ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $review->trainer->user->email ?? 'N/A' }}</p>
        </div>

        <div class="card">
            <h3>Member</h3>
            <p><strong>Name:</strong> {{ $review->member->user->name ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $review->member->user->email ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="card" style="margin-top: 1.5rem;">
        <h3>Review</h3>
        <p><strong>Rating:</strong> <span style="color: #ffd700; font-size: 1.2rem;">★ {{ $review->rating }}/5</span></p>
        <p><strong>Comment:</strong></p>
        <p style="line-height: 1.8;">{{ $review->comment ?? 'No comment provided' }}</p>
        <p style="color: #a9a89d; font-size: 0.9rem;">Posted on {{ $review->created_at->format('M d, Y') }}</p>
    </div>

    <div style="margin-top: 1.5rem;">
        <form action="{{ route('reviews.destroy', $review) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('This will permanently delete this review. Are you sure?')">Delete Review</button>
        </form>
    </div>
@endsection
