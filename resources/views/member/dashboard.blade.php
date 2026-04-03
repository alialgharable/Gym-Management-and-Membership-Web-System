@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">My Dashboard</h1>
            <p class="section-subtitle">Welcome to your personal gym dashboard</p>
        </div>
    </div>

    @if ($member)
        <div class="card-grid">
            <div class="card" style="background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);">
                <h3 style="color: #ffffff; margin-top: 0;">My Subscription</h3>
                @php
                    $activeSub = $member->subscription()->where('status', 'active')->first();
                    $planName = $activeSub && $activeSub->plan ? $activeSub->plan->name : null;
                @endphp
                @if ($activeSub && $planName)
                    <p style="color: #a8d5a8; margin: 0.5rem 0;"><strong>{{ $planName }}</strong></p>
                    <p style="color: #a8d5a8; font-size: 0.9rem;">Valid until: {{ $activeSub->end_date->format('M d, Y') }}</p>
                @else
                    <p style="color: #ff5555;">No active subscription</p>
                @endif
            </div>

            <div class="card" style="background: linear-gradient(135deg, #1976d2 0%, #0d47a1 100%);">
                <h3 style="color: #ffffff; margin-top: 0;">Total Bookings</h3>
                <p style="font-size: 2.5rem; font-weight: 700; color: #ffffff; margin: 0;">{{ $member->bookings->count() }}</p>
                <p style="color: #90caf9; font-size: 0.9rem;">Classes booked</p>
            </div>

            <div class="card" style="background: linear-gradient(135deg, #f57c00 0%, #bf360c 100%);">
                <h3 style="color: #ffffff; margin-top: 0;">Confirmed Bookings</h3>
                <p style="font-size: 2.5rem; font-weight: 700; color: #ffffff; margin: 0;">{{ $member->bookings->where('status', 'confirmed')->count() }}</p>
                <p style="color: #ffb74d; font-size: 0.9rem;">Upcoming classes</p>
            </div>

            <div class="card" style="background: linear-gradient(135deg, #c2185b 0%, #880e4f 100%);">
                <h3 style="color: #ffffff; margin-top: 0;">Member Since</h3>
                <p style="color: #f48fb1;">{{ $member->created_at->format('M d, Y') }}</p>
                <p style="color: #f48fb1; font-size: 0.9rem;">({{ $member->created_at->diffInMonths(now()) }} months)</p>
            </div>
        </div>

        <div class="card" style="margin-top: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3>My Bookings</h3>
                <a href="{{ route('bookings.create') }}" class="btn btn-primary">+ Book Class</a>
            </div>
            
            @if ($member->bookings->count())
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Trainer</th>
                                <th>Schedule</th>
                                <th>Status</th>
                                <th>Booked On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($member->bookings as $booking)
                                @php
                                    $className = optional($booking->gymClass)->name ?? 'N/A';
                                    $trainerName = optional(optional($booking->gymClass)->trainer)->user->name ?? 'N/A';
                                    $schedule = optional($booking->gymClass)->schedule ?? 'N/A';
                                @endphp
                                <tr>
                                    <td>{{ $className }}</td>
                                    <td>{{ $trainerName }}</td>
                                    <td>{{ $schedule }}</td>
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
                <p style="color: #a9a89d;">No bookings yet. <a href="{{ route('bookings.create') }}" style="color: #f7d34a; font-weight: bold;">Book a class now</a></p>
            @endif
        </div>
    @else
        <div class="alert alert-warning">
            Member profile not found. Please contact support.
        </div>
    @endif
@endsection