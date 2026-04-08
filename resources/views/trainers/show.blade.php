@extends('layouts.app')

@section('title', 'Trainer Details')

@section('content')
    @php
        $user = $trainer->user;

        $profileImage = $user && $user->profile_picture
            ? asset('storage/' . $user->profile_picture)
            : asset('images/default-avatar.png');
    @endphp

    <div class="page-header">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <img src="{{ $profileImage }}" alt="{{ $trainer->user->name ?? 'Trainer' }}"
                style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 2px solid #ffd54a;"
                onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';">

            <div>
                <h1 class="section-title" style="margin: 0;">
                    {{ $trainer->user->name ?? 'Trainer' }}
                </h1>
                <p class="section-subtitle" style="margin: 0.2rem 0 0;">
                    Trainer Profile
                </p>
            </div>
        </div>

        <div class="actions">
            @if(auth()->check() && auth()->user()->trainer && auth()->user()->trainer->id === $trainer->id)
                <a href="{{ route('trainer.dashboard') }}" class="btn btn-secondary">← Back to Dashboard</a>
            @else
                <a href="{{ route('trainers.index') }}" class="btn btn-secondary">← Back</a>
            @endif

            @auth
                @if(auth()->id() === $trainer->user_id)
                    <a href="{{ route('trainers.edit', $trainer) }}" class="btn btn-primary">Edit</a>
                @endif
            @endauth
        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: 1fr 1fr;">
        <div class="card">
            <h3>Personal Information</h3>
            <p><strong>Name:</strong> {{ $trainer->user->name ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $trainer->user->email ?? 'N/A' }}</p>
            <p><strong>Specialization:</strong> {{ $trainer->specialty ?? 'N/A' }}</p>
        </div>

        <div class="card">
            <h3>Statistics</h3>
            <p><strong>Classes:</strong> {{ $trainer->gymClasses->count() }}</p>
            <p><strong>Reviews:</strong> {{ $trainer->reviews->count() }}</p>
            <p><strong>Avg Rating:</strong>
                {{ $trainer->reviews->count() > 0 ? number_format($trainer->reviews->avg('rating'), 1) : 'N/A' }}/5
            </p>
        </div>
    </div>

    @if ($trainer->gymClasses->count())
        <div class="card" style="margin-top: 1.5rem;">
            <h3>Classes ({{ $trainer->gymClasses->count() }})</h3>
            <ul style="list-style: none; padding: 0;">
                @foreach ($trainer->gymClasses as $class)
                    <li style="padding: 10px 0; border-bottom: 1px solid #2b2b2b;">
                        <a href="{{ route('classes.show', $class) }}" style="color: #f7d34a; font-weight: 600;">
                            {{ $class->name }}
                        </a>
                        <span style="color: #a9a89d;">
                            {{ $class->schedule ?? 'N/A' }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($trainer->reviews->count())
        <div class="card" style="margin-top: 1.5rem;">
            <h3>Recent Reviews</h3>
            <ul style="list-style: none; padding: 0;">
                @foreach ($trainer->reviews->take(5) as $review)
                    <li style="padding: 10px 0; border-bottom: 1px solid #2b2b2b;">
                        <strong>{{ $review->member->user->name ?? 'Anonymous' }}</strong>
                        <span style="color: #ffd700;">★ {{ $review->rating }}/5</span>
                        <p style="margin: 5px 0;">
                            {{ $review->comment ?? 'No comment' }}
                        </p>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @auth
        @if(auth()->id() === $trainer->user_id)
            <div style="margin-top: 1.5rem;">
                <form action="{{ route('trainers.destroy', $trainer) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('This will permanently delete this trainer. Are you sure?')">
                        Delete Profile
                    </button>
                </form>
            </div>
        @endif
    @endauth
@endsection