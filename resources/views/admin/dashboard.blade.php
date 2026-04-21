@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

    <style>
        .admin-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }

        .admin-stat-card {
            padding: 1.2rem;
            border-radius: 18px;
            background: linear-gradient(160deg, rgba(20, 34, 54, 0.94), rgba(10, 18, 30, 0.95));
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 14px 38px rgba(0, 0, 0, 0.22);
        }

        .admin-stat-card--wide {
            grid-column: 1 / -1;
        }

        .admin-stat-title {
            margin: 0;
            color: #f8f7ec;
            font-size: 1.02rem;
        }

        .admin-stat-value {
            margin: 0.55rem 0 0.4rem;
            font-size: 2.4rem;
            font-weight: 700;
            color: #ffd54f;
            line-height: 1.1;
        }

        .admin-stat-link {
            color: #d6c36f;
            font-weight: 600;
        }

        .admin-stat-link:hover {
            color: #ffe082;
        }

        .admin-empty-text {
            color: #b7b39c;
        }

        .status-positive {
            color: #5fd68f;
        }

        .status-inactive {
            color: #ff6b6b;
        }

        .status-warning {
            color: #ffd54f;
        }
    </style>

    <div class="page-header">
        <div>
            <h1 class="section-title">Admin Dashboard</h1>
            <p class="section-subtitle">System overview and performance insights</p>
        </div>
    </div>

    <div class="admin-stats-grid">

        <div class="admin-stat-card">
            <h3 class="admin-stat-title">Members</h3>
            <p class="admin-stat-value">{{ $stats['total_members'] }}</p>
            <a href="{{ route('members.index') }}" class="admin-stat-link">View all →</a>
        </div>

        <div class="admin-stat-card">
            <h3 class="admin-stat-title">Subscriptions</h3>
            <p class="admin-stat-value">{{ $stats['active_subscriptions'] }}</p>
            <a href="{{ route('subscriptions.index') }}" class="admin-stat-link">View →</a>
        </div>

        <div class="admin-stat-card">
            <h3 class="admin-stat-title">Classes</h3>
            <p class="admin-stat-value">{{ $stats['total_classes'] }}</p>
            <a href="{{ route('classes.index') }}" class="admin-stat-link">Manage →</a>
        </div>

        <div class="admin-stat-card">
            <h3 class="admin-stat-title">Trainers</h3>
            <p class="admin-stat-value">{{ $stats['total_trainers'] }}</p>
            <a href="{{ route('trainers.index') }}" class="admin-stat-link">View →</a>
        </div>

        <div class="admin-stat-card admin-stat-card--wide">
            <h3 class="admin-stat-title">Bookings</h3>
            <p class="admin-stat-value">{{ $stats['total_bookings'] }}</p>
            <a href="{{ route('bookings.index') }}" class="admin-stat-link">View all →</a>
        </div>

    </div>

    <div class="card" style="margin-top:1.5rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <h3>Recent Members</h3>
            <a href="{{ route('members.index') }}" class="btn btn-secondary btn-sm">View All</a>
        </div>

        @if ($recentMembers->count())
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentMembers as $member)
                            @php
                                $name = optional($member->user)->name ?? 'N/A';
                                $email = optional($member->user)->email ?? 'N/A';
                                $activeSub = $member->subscription()->where('status', 'active')->first();
                                $planName = $activeSub && $activeSub->plan ? $activeSub->plan->name : null;
                            @endphp
                            <tr>
                                <td>{{ $name }}</td>
                                <td>{{ $email }}</td>
                                <td>{{ $member->created_at->format('M d, Y') }}</td>
                                <td>
                                    <span class="{{ $planName ? 'status-positive' : 'status-inactive' }}">
                                        {{ $planName ?? 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="admin-empty-text">No recent members.</p>
        @endif
    </div>

    <div class="card" style="margin-top:1.5rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <h3>Recent Bookings</h3>
            <a href="{{ route('bookings.index') }}" class="btn btn-secondary btn-sm">View All</a>
        </div>

        @if ($recentBookings->count())
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Class</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentBookings as $booking)
                            @php
                                $memberName = optional($booking->member)->user->name ?? 'N/A';
                                $className = optional($booking->gymClass)->name ?? 'N/A';
                            @endphp
                            <tr>
                                <td>{{ $memberName }}</td>
                                <td>{{ $className }}</td>
                                <td>
                                    <span class="{{ $booking->status === 'confirmed' ? 'status-positive' : 'status-warning' }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td>{{ $booking->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="admin-empty-text">No recent bookings.</p>
        @endif
    </div>

@endsection