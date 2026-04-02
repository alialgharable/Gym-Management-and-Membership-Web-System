@extends('layouts.app')

@section('title', 'Trainer Dashboard')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Trainer Dashboard</h1>
            <p class="section-subtitle">Track classes, bookings, and feedback from members</p>
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
                <p style="font-size: 2.5rem; font-weight: 700; color: #ffffff; margin: 0;">{{ $avgRating }}</p>
                <p style="color: #ffb74d; font-size: 0.9rem;">Based on {{ $trainer->reviews->count() }} reviews</p>
            </div>
        </div>

        <div class="card" style="margin-top: 1.5rem;">
            <h3>My Classes</h3>
            @if ($trainer->gymClasses->count())
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Class Name</th>
                                <th>Schedule</th>
                                <th>Capacity</th>
                                <th>Bookings</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trainer->gymClasses as $gymClass)
                                <tr>
                                    <td>{{ $gymClass->name }}</td>
                                    <td>{{ $gymClass->schedule }}</td>
                                    <td>{{ $gymClass->capacity }}</td>
                                    <td>{{ $gymClass->bookings->count() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="color: #a9a89d;">No classes assigned yet.</p>
            @endif
        </div>
    @else
        <div class="alert alert-warning">
            Trainer profile not found. Please contact support.
        </div>
    @endif
@endsection
