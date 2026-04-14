@extends('layouts.app')

@section('title', 'Trainer Dashboard')

@section('content')

    @if ($trainer)
        @php
            $totalClasses = $trainer->gymClasses->count();
            $totalBookings = $trainer->gymClasses->sum(fn($gymClass) => $gymClass->bookings->count());
            $avgRating = round($trainer->reviews->avg('rating') ?? 0, 1);
        @endphp

        <div class="page-header">
            <div>
                <h1 class="section-title">Trainer Dashboard</h1>
                <p class="section-subtitle">Welcome back, {{ $trainer->user->name ?? 'Trainer' }}</p>
            </div>
        </div>

        <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">

            <div class="card" style="background: linear-gradient(135deg, #2e7d32, #1b5e20);">
                <h3 style="color:#fff;">Classes</h3>
                <p style="font-size:2.5rem; font-weight:700; color:#fff;">{{ $totalClasses }}</p>
                <p style="color:#a8d5a8; font-size:0.9rem;">Assigned to you</p>
            </div>

            <div class="card" style="background: linear-gradient(135deg, #1976d2, #0d47a1);">
                <h3 style="color:#fff;">Bookings</h3>
                <p style="font-size:2.5rem; font-weight:700; color:#fff;">{{ $totalBookings }}</p>
                <p style="color:#90caf9; font-size:0.9rem;">Across all classes</p>
            </div>

            <div class="card" style="background: linear-gradient(135deg, #f57c00, #bf360c);">
                <h3 style="color:#fff;">Rating</h3>
                <p style="font-size:2.5rem; font-weight:700; color:#fff;">⭐ {{ $avgRating }}</p>
                <p style="color:#ffb74d; font-size:0.9rem;">{{ $trainer->reviews->count() }} reviews</p>
            </div>

        </div>

        <div class="card" style="margin-top:1.5rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                <h3 style="margin:0;">My Classes</h3>
                <a href="{{ route('classes.create') }}" class="btn btn-primary">
                    Create Class
                </a>
            </div>

            @if ($trainer->gymClasses->count())
                <div style="overflow-x:auto; margin-top:1rem;">
                    <table>
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Schedule</th>
                                <th>Capacity</th>
                                <th>Bookings</th>
                                <th></th>
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
                                            <a href="{{ route('classes.show', $gymClass) }}" class="btn btn-secondary">
                                                View
                                            </a>
                                            <a href="{{ route('classes.edit', $gymClass) }}" class="btn btn-secondary">
                                                Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="color:#aaa; margin-top:1rem;">
                    No classes yet. Create your first one.
                </p>
            @endif
        </div>

        @if ($trainer->reviews->count())
            <div class="card" style="margin-top:1.5rem;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h3 style="margin:0;">Reviews</h3>
                    <span style="color:#aaa;">{{ $trainer->reviews->count() }} total</span>
                </div>

                <div style="display:grid; gap:1rem; margin-top:1rem; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
                    @foreach ($trainer->reviews as $review)
                        <div style="background:#0f0f0f; border:1px solid #2b2b2b; border-radius:12px; padding:1rem;">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.5rem;">
                                <div>
                                    <p style="margin:0; font-weight:600;">
                                        {{ $review->member->user->name }}
                                    </p>
                                    <p style="margin:0; font-size:0.85rem; color:#a9a89d;">
                                        {{ $review->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                                <span style="background:#ffd54f; color:#111; padding:4px 10px; border-radius:8px; font-weight:700;">
                                    ⭐ {{ $review->rating }}
                                </span>
                            </div>

                            <p style="margin:0; color:#d7d2ad; font-size:0.95rem;">
                                {{ $review->comment }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    @else
        <div class="card" style="text-align:center; padding:40px;">
            <h3 style="color:#ff5555;">Profile Not Found</h3>
            <p style="color:#aaa;">Please contact support.</p>
        </div>
    @endif

@endsection