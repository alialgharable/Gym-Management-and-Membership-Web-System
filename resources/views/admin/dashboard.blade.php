@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Admin Dashboard</h1>
            <p class="section-subtitle">System Overview and Statistics</p>
        </div>
    </div>

    <div class="card-grid">
        <div class="card" style="background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);">
            <h3 style="color: #ffffff; margin-top: 0;">Total Members</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #ffffff; margin: 0;">{{ $stats['total_members'] }}</p>
            <a href="{{ route('members.index') }}" style="color: #a8d5a8; text-decoration: underline;">View all members →</a>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #1976d2 0%, #0d47a1 100%);">
            <h3 style="color: #ffffff; margin-top: 0;">Active Subscriptions</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #ffffff; margin: 0;">{{ $stats['active_subscriptions'] }}</p>
            <a href="{{ route('subscriptions.index') }}" style="color: #90caf9; text-decoration: underline;">View subscriptions →</a>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #f57c00 0%, #bf360c 100%);">
            <h3 style="color: #ffffff; margin-top: 0;">Total Classes</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #ffffff; margin: 0;">{{ $stats['total_classes'] }}</p>
            <a href="{{ route('classes.index') }}" style="color: #ffb74d; text-decoration: underline;">Manage classes →</a>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #c2185b 0%, #880e4f 100%);">
            <h3 style="color: #ffffff; margin-top: 0;">Trainers</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #ffffff; margin: 0;">{{ $stats['total_trainers'] }}</p>
            <a href="{{ route('trainers.index') }}" style="color: #f48fb1; text-decoration: underline;">View trainers →</a>
        </div>

        <div class="card" style="background: linear-gradient(135deg, #6a1b9a 0%, #38006b 100%); grid-column: 1 / -1;">
            <h3 style="color: #ffffff; margin-top: 0;">Total Bookings</h3>
            <p style="font-size: 2.5rem; font-weight: 700; color: #ffffff; margin: 0;">{{ $stats['total_bookings'] }}</p>
            <a href="{{ route('bookings.index') }}" style="color: #ce93d8; text-decoration: underline;">View all bookings →</a>
        </div>
    </div>

    <div class="card" style="margin-top: 1.5rem;">
        <h3>Recent Members</h3>
        @if ($recentMembers->count())
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Member Since</th>
                            <th>Subscription</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentMembers as $member)
                            @php
                                $memberName = optional($member->user)->name ?? 'N/A';
                                $memberEmail = optional($member->user)->email ?? 'N/A';
                            @endphp
                            <tr>
                                <td>{{ $memberName }}</td>
                                <td>{{ $memberEmail }}</td>
                                <td>{{ $member->created_at->format('M d, Y') }}</td>
                                <td>
                                    @php
                                        $activeSub = $member->subscription()->where('status', 'active')->first();
                                        $activePlanName = $activeSub && $activeSub->plan ? $activeSub->plan->name : null;
                                    @endphp
                                    <span style="color: {{ $activePlanName ? '#5fd68f' : '#ff5555' }};">
                                        {{ $activePlanName ?? 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="card" style="margin-top: 1.5rem;">
        <h3>Recent Bookings</h3>
        @if ($recentBookings->count())
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Class</th>
                            <th>Status</th>
                            <th>Booked</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentBookings as $booking)
                            @php
                                $bookingMemberName = optional($booking->member)->user->name ?? 'N/A';
                                $bookingClassName = optional($booking->gymClass)->name ?? 'N/A';
                            @endphp
                            <tr>
                                <td>{{ $bookingMemberName }}</td>
                                <td>{{ $bookingClassName }}</td>
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
        @endif
    </div>
@endsection
