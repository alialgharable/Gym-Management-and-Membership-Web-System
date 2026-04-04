@extends('layouts.app')

@section('title', 'Trainer Dashboard')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Trainer Dashboard</h1>
            <p class="section-subtitle">Track classes, bookings, and feedback from members</p>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="{{ route('trainers.show', auth()->user()->trainer->id) }}" class="btn btn-primary">View Profile</a>
            <a href="{{ route('trainers.edit', auth()->user()->trainer->id) }}" class="btn btn-secondary">Edit Profile</a>
        </div>
    </div>

    @if ($trainer)
        @php
            $totalClasses = $trainer->gymClasses->count();
            $totalBookings = $trainer->gymClasses->sum(fn($gymClass) => $gymClass->bookings->count());
            $avgRating = round($trainer->reviews->avg('rating') ?? 0, 1);
        @endphp

        <div class="card-grid">
            <div class="card" style="background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);">
                <h3 style="color: #ffffff; margin-top: 0;">Assigned Classes</h3>
                <p style="font-size: 2.5rem; font-weight: 700; color: #ffffff; margin: 0;">{{ $totalClasses }}</p>
                <p style="color: #a8d5a8; font-size: 0.9rem;">Classes under your schedule</p>
            </div>

            <div class="card" style="background: linear-gradient(135deg, #1976d2 0%, #0d47a1 100%);">
                <h3 style="color: #ffffff; margin-top: 0;">Total Bookings</h3>
                <p style="font-size: 2.5rem; font-weight: 700; color: #ffffff; margin: 0;">{{ $totalBookings }}</p>
                <p style="color: #90caf9; font-size: 0.9rem;">Bookings across all your classes</p>
            </div>

            <div class="card" style="background: linear-gradient(135deg, #f57c00 0%, #bf360c 100%);">
                <h3 style="color: #ffffff; margin-top: 0;">Average Rating</h3>
                <p style="font-size: 2.5rem; font-weight: 700; color: #ffffff; margin: 0;">⭐ {{ $avgRating }}</p>
                <p style="color: #ffb74d; font-size: 0.9rem;">Based on {{ $trainer->reviews->count() }} reviews</p>
                <a href="#reviews" style="color: #ffb74d; text-decoration: underline; font-size: 0.85rem;">View all reviews →</a>
            </div>
        </div>

        <div class="card" style="margin-top: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0;">My Classes</h3>
                <a href="{{ route('classes.create') }}" class="btn btn-primary" style="font-size: 0.9rem;">+ Create Class</a>
            </div>
            @if ($trainer->gymClasses->count())
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Class Name</th>
                                <th>Schedule</th>
                                <th>Capacity</th>
                                <th>Bookings</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trainer->gymClasses as $gymClass)
                                <tr>
                                    <td>{{ $gymClass->name }}</td>
                                    <td>{{ $gymClass->schedule }}</td>
                                    <td>{{ $gymClass->capacity }}</td>
                                    <td>{{ $gymClass->bookings->count() }}</td>
                                    <td>
                                        <div class="actions">
                                            <a href="{{ route('classes.show', $gymClass) }}" class="btn btn-secondary" style="font-size: 0.85rem;">View</a>
                                            <a href="{{ route('classes.edit', $gymClass) }}" class="btn btn-secondary" style="font-size: 0.85rem;">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="color: #a9a89d; margin-bottom: 1rem;">No classes created yet. Click the button above to create your first class.</p>
            @endif
        </div>

        @if ($trainer->reviews->count())
            <div class="card" style="margin-top: 1.5rem;" id="reviews">
                <h3>Member Reviews</h3>
                <div style="display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                    @foreach ($trainer->reviews as $review)
                        <div style="
                            background: #0f0f0f;
                            border: 1px solid #2b2b2b;
                            border-radius: 12px;
                            padding: 1rem;
                        ">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                <div>
                                    <p style="margin: 0; font-weight: 600; color: #f8f7ec;">{{ $review->member->user->name }}</p>
                                    <p style="margin: 0; font-size: 0.85rem; color: #a9a89d;">{{ $review->created_at->format('M d, Y') }}</p>
                                </div>
                                <div style="
                                    background: #ffd54f;
                                    color: #111111;
                                    padding: 0.25rem 0.75rem;
                                    border-radius: 8px;
                                    font-weight: 700;
                                    font-size: 0.9rem;
                                ">
                                    ⭐ {{ $review->rating }}
                                </div>
                            </div>
                            <p style="
                                margin: 0;
                                color: #d7d2ad;
                                line-height: 1.5;
                                font-size: 0.95rem;
                            ">{{ $review->comment }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <div class="alert alert-warning">
            Trainer profile not found. Please contact support.
        </div>
    @endif
@endsection
