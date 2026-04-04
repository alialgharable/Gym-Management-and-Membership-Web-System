@extends('layouts.app')

@section('title', 'Bookings')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Class Bookings</h1>
            <p class="section-subtitle">Manage member bookings for gym classes</p>
        </div>
        @if(auth()->check() && (auth()->user()->isMember() || auth()->user()->isTrainer() || auth()->user()->isAdmin()))
            <a href="{{ route('bookings.create') }}" class="btn btn-primary">+ New Booking</a>
        @endif
    </div>

    @if ($bookings->count())
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Member</th>
                        <th>Class</th>
                        <th>Status</th>
                        <th>Booked On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookings as $booking)
                        <tr>
                            <td>#{{ $booking->id }}</td>
                            <td>{{ $booking->member->user->name ?? 'N/A' }}</td>
                            <td>{{ $booking->gymClass->name ?? 'N/A' }}</td>
                            <td>
                                <span style="color: {{ $booking->status === 'confirmed' ? '#5fd68f' : '#ffd700' }};">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td>{{ $booking->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary">View</a>
                                    <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-secondary">Edit</a>
                                    <form action="{{ route('bookings.destroy', $booking) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-warning">
            No bookings found. <a href="{{ route('bookings.create') }}" style="color: #ffd700; font-weight: bold;">Create one now</a>
        </div>
    @endif
@endsection