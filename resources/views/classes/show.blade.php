@extends('layouts.app')

@section('title', 'Class Details')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">{{ $gymClass->name }}</h1>
            <p class="section-subtitle">Class Details</p>
        </div>
        <div class="actions">
            <a href="{{ route('classes.index') }}" class="btn btn-secondary">← Back</a>
            <a href="{{ route('classes.edit', $gymClass) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: 1fr 1fr;">
        <div class="card">
            <h3>Class Information</h3>
            <p><strong>Trainer:</strong> {{ $gymClass->trainer->user->name ?? 'N/A' }}</p>
            <p><strong>Schedule:</strong> {{ $gymClass->schedule ?? 'N/A' }}</p>
            <p><strong>Capacity:</strong> {{ $gymClass->capacity ?? 'N/A' }} members</p>
            <p><strong>Description:</strong> {{ $gymClass->description ?? 'No description' }}</p>
        </div>

        <div class="card">
            <h3>Bookings</h3>
            <p><strong>Total Bookings:</strong> {{ $gymClass->bookings->count() }}</p>
            <p><strong>Availability:</strong> {{ $gymClass->capacity - $gymClass->bookings->count() }}/{{ $gymClass->capacity }}</p>
        </div>
    </div>

    @if ($gymClass->bookings->count())
        <div class="card" style="margin-top: 1.5rem;">
            <h3>Members Booked</h3>
            <ul style="list-style: none; padding: 0;">
                @foreach ($gymClass->bookings as $booking)
                    <li style="padding: 10px 0; border-bottom: 1px solid #2b2b2b;">
                        <strong>{{ $booking->member->user->name ?? 'N/A' }}</strong>
                        <span style="color: {{ $booking->status === 'confirmed' ? '#5fd68f' : '#ffd700' }};">{{ ucfirst($booking->status) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="margin-top: 1.5rem;">
        <form action="{{ route('classes.destroy', $gymClass) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('This will permanently delete this class. Are you sure?')">Delete Class</button>
        </form>
    </div>
@endsection
