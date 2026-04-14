@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="section-title">Admin Dashboard</h1>
            <p class="section-subtitle">System overview and performance insights</p>
        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">

        <div class="card" style="background: linear-gradient(135deg, #2e7d32, #1b5e20);">
            <h3 style="color:#fff;">Members</h3>
            <p style="font-size:2.5rem; font-weight:700; color:#fff;">{{ $stats['total_members'] }}</p>
            <a href="{{ route('members.index') }}" style="color:#a8d5a8;">View all →</a>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #1976d2, #0d47a1);">
            <h3 style="color:#fff;">Subscriptions</h3>
            <p style="font-size:2.5rem; font-weight:700; color:#fff;">{{ $stats['active_subscriptions'] }}</p>
            <a href="{{ route('subscriptions.index') }}" style="color:#90caf9;">View →</a>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #f57c00, #bf360c);">
            <h3 style="color:#fff;">Classes</h3>
            <p style="font-size:2.5rem; font-weight:700; color:#fff;">{{ $stats['total_classes'] }}</p>
            <a href="{{ route('classes.index') }}" style="color:#ffb74d;">Manage →</a>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #c2185b, #880e4f);">
            <h3 style="color:#fff;">Trainers</h3>
            <p style="font-size:2.5rem; font-weight:700; color:#fff;">{{ $stats['total_trainers'] }}</p>
            <a href="{{ route('trainers.index') }}" style="color:#f48fb1;">View →</a>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #6a1b9a, #38006b); grid-column: 1 / -1;">
            <h3 style="color:#fff;">Bookings</h3>
            <p style="font-size:2.5rem; font-weight:700; color:#fff;">{{ $stats['total_bookings'] }}</p>
            <a href="{{ route('bookings.index') }}" style="color:#ce93d8;">View all →</a>
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
                                    <span style="color: {{ $planName ? '#5fd68f' : '#ff5555' }};">
                                        {{ $planName ?? 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="color:#aaa;">No recent members.</p>
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
                                    <span style="color: {{ $booking->status === 'confirmed' ? '#5fd68f' : '#ffd700' }};">
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
            <p style="color:#aaa;">No recent bookings.</p>
        @endif
    </div>

@endsection