@extends('layouts.app')

@section('title', 'Bookings')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">All Bookings</h1>
            <p class="section-subtitle">Review your upcoming classes and booking status in a clean dashboard layout.</p>
        </div>
        <a href="{{ route('bookings.create') }}" class="field-button">Create Booking</a>
    </div>

    @if($bookings->isEmpty())
        <div class="alert">
            No bookings found yet. Create a new booking to get started.
        </div>
    @else
        <div class="card-grid">
            @foreach($bookings as $booking)
                <article class="card">
                    <h2>{{ $booking->gymClass->name ?? 'Unassigned Class' }}</h2>
                    <p><strong>Status:</strong> {{ ucfirst($booking->status) }}</p>
                    <p><strong>Date:</strong> {{ optional($booking->scheduled_at)->format('F j, Y g:i A') ?? 'Not scheduled' }}</p>
                </article>
            @endforeach
        </div>
    @endif
@endsection