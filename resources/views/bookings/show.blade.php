@extends('layouts.app')

@section('content')

    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1>Booking Details</h1>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Back to Bookings</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5>Booking ID</h5>
                        <p>{{ $booking->id }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Status</h5>
                        <p>
                            <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : 'warning' }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5>Member</h5>
                        <p>
                            <a href="{{ route('members.show', $booking->member) }}">
                                {{ $booking->member->user->name ?? 'N/A' }}
                            </a>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5>Email</h5>
                        <p>{{ $booking->member->user->email ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5>Class</h5>
                        <p>{{ $booking->gymClass->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Trainer</h5>
                        <p>{{ $booking->gymClass->trainer->user->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5>Schedule</h5>
                        <p>{{ $booking->gymClass->schedule ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Capacity</h5>
                        <p>{{ $booking->gymClass->capacity ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h5>Booked On</h5>
                        <p>{{ $booking->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Last Updated</h5>
                        <p>{{ $booking->updated_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-primary">Edit</a>
                    <form action="{{ route('bookings.destroy', $booking) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
