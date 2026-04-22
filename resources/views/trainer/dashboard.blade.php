@extends('layouts.app')

@section('title', 'Trainer Dashboard')

@section('content')

    <style>
        .trainer-stats-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .trainer-stat-card {
            padding: 1.2rem;
            border-radius: 18px;
            background: linear-gradient(160deg, rgba(20, 34, 54, 0.94), rgba(10, 18, 30, 0.95));
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 14px 38px rgba(0, 0, 0, 0.22);
        }

        .trainer-stat-card h3 {
            margin: 0;
            color: #f8f7ec;
            font-size: 1.02rem;
        }

        .trainer-stat-value {
            margin: 0.55rem 0 0.4rem;
            font-size: 2.4rem;
            font-weight: 700;
            color: #ffd54f;
            line-height: 1.1;
        }

        .trainer-stat-meta {
            margin: 0;
            color: #bdb89c;
            font-size: 0.9rem;
        }

        .trainer-block {
            margin-top: 1.5rem;
        }

        .trainer-block-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.65rem;
        }

        .trainer-block-title {
            margin: 0;
            color: #f8f7ec;
        }

        .trainer-block-subtle {
            color: #a9a89d;
        }

        .trainer-empty-text {
            color: #b7b39c;
            margin-top: 1rem;
        }

        .trainer-reviews-grid {
            display: grid;
            gap: 1rem;
            margin-top: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        .trainer-review-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 1rem;
        }

        .trainer-review-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
            gap: 0.7rem;
        }

        .trainer-review-name {
            margin: 0;
            font-weight: 600;
            color: #f3f1e8;
        }

        .trainer-review-date {
            margin: 0;
            font-size: 0.85rem;
            color: #a9a89d;
        }

        .trainer-review-rating {
            background: #ffd54f;
            color: #111;
            padding: 4px 10px;
            border-radius: 8px;
            font-weight: 700;
            white-space: nowrap;
        }

        .trainer-review-comment {
            margin: 0;
            color: #d7d2ad;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .trainer-profile-missing {
            text-align: center;
            padding: 40px;
        }

        .trainer-profile-missing h3 {
            color: #ff5555;
        }

        .trainer-profile-missing p {
            color: #aaa;
        }
    </style>

    @if ($trainer)
        @php
            $totalClasses = $trainer->gymClasses->count();
            $totalBookings = $trainer->gymClasses->sum(fn($gymClass) => $gymClass->bookings->count());
            $avgRating = round($trainer->reviews->avg('rating') ?? 0, 1);
            $salary = $trainer->salary;
        @endphp

        <div class="page-header">
            <div>
                <h1 class="section-title">Trainer Dashboard</h1>
                <p class="section-subtitle">Welcome back, {{ $trainer->user->name ?? 'Trainer' }}</p>
            </div>
        </div>

        <div class="trainer-stats-grid">

            <div class="trainer-stat-card">
                <h3>Classes</h3>
                <p class="trainer-stat-value">{{ $totalClasses }}</p>
                <p class="trainer-stat-meta">Assigned to you</p>
            </div>

            <div class="trainer-stat-card">
                <h3>Bookings</h3>
                <p class="trainer-stat-value">{{ $totalBookings }}</p>
                <p class="trainer-stat-meta">Across all classes</p>
            </div>

            <div class="trainer-stat-card">
                <h3>Rating</h3>
                <p class="trainer-stat-value">⭐ {{ $avgRating }}</p>
                <p class="trainer-stat-meta">{{ $trainer->reviews->count() }} reviews</p>
            </div>

            <div class="trainer-stat-card">
                <h3>Salary</h3>
                <p class="trainer-stat-value">${{ number_format((float) $salary, 2) }}</p>
                <p class="trainer-stat-meta">Your monthly salary</p>
            </div>

        </div>

        <div class="card trainer-block" id="premium-requests">
            <div class="trainer-block-header">
                <h3 class="trainer-block-title">Premium Coach Requests</h3>
                <span class="trainer-block-subtle">{{ $pendingPremiumRequests->count() }} pending</span>
            </div>

            @if ($pendingPremiumRequests->count())
                <div style="overflow-x:auto; margin-top:1rem;">
                    <table>
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Plan</th>
                                <th>Requested</th>
                                <th>Note</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingPremiumRequests as $coachRequest)
                                <tr>
                                    <td>{{ $coachRequest->member->user->name ?? 'Member #' . $coachRequest->member_id }}</td>
                                    <td>
                                        {{ $coachRequest->subscription?->plan?->tierLabel() ?? 'Premium' }}
                                        ({{ $coachRequest->subscription?->plan?->durationLabel() ?? 'N/A' }})
                                    </td>
                                    <td>{{ $coachRequest->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ $coachRequest->member_note ?: 'No note' }}</td>
                                    <td>
                                        <div class="actions" style="display:flex; gap:8px; flex-wrap:wrap;">
                                            <form action="{{ route('premium-coach-requests.approve', $coachRequest) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button class="btn btn-success" type="submit">Approve</button>
                                            </form>

                                            <form action="{{ route('premium-coach-requests.reject', $coachRequest) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button class="btn btn-danger" type="submit">Reject</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="trainer-empty-text">No pending premium requests right now.</p>
            @endif
        </div>

        <div class="card trainer-block">
            <div class="trainer-block-header">
                <h3 class="trainer-block-title">My Classes</h3>
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
                <p class="trainer-empty-text">
                    No classes yet. Create your first one.
                </p>
            @endif
        </div>

        @if ($trainer->reviews->count())
            <div class="card trainer-block">
                <div class="trainer-block-header">
                    <h3 class="trainer-block-title">Reviews</h3>
                    <span class="trainer-block-subtle">{{ $trainer->reviews->count() }} total</span>
                </div>

                <div class="trainer-reviews-grid">
                    @foreach ($trainer->reviews as $review)
                        <div class="trainer-review-card">
                            <div class="trainer-review-top">
                                <div>
                                    <p class="trainer-review-name">
                                        {{ $review->member->user->name }}
                                    </p>
                                    <p class="trainer-review-date">
                                        {{ $review->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                                <span class="trainer-review-rating">
                                    ⭐ {{ $review->rating }}
                                </span>
                            </div>

                            <p class="trainer-review-comment">
                                {{ $review->comment }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    @else
        <div class="card trainer-profile-missing">
            <h3>Profile Not Found</h3>
            <p>Please contact support.</p>
        </div>
    @endif

@endsection