@extends('layouts.app')

@section('title', 'Class Details')

@section('content')

    @php
        $bookingsCount = $gymClass->bookings->count();
        $capacity = $gymClass->capacity ?? 0;
        $availableSpots = max($capacity - $bookingsCount, 0);
    @endphp

    <div class="page-header">
        <div>
            <h1 class="section-title">{{ $gymClass->name }}</h1>
            <p class="section-subtitle">Class details and booking overview</p>
        </div>

        <div class="actions">
            <a href="{{ route('classes.index') }}" class="btn btn-secondary">Back</a>

            @auth
                @if(auth()->user()->isTrainer() || auth()->user()->isAdmin())
                    <a href="{{ route('classes.edit', $gymClass) }}" class="btn btn-primary">Edit</a>
                @endif
            @endauth
        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
        <div class="card">
            <h3>Class Information</h3>
            <p><strong>Category:</strong> {{ str_replace('_', ' ', ucfirst($gymClass->category ?? 'N/A')) }}</p>
            <p><strong>Trainer:</strong> {{ $gymClass->trainer->user->name ?? 'N/A' }}</p>
            <p><strong>Room:</strong> {{ $gymClass->room->name ?? 'N/A' }}</p>
            <p><strong>Schedule:</strong> {{ $gymClass->schedule ?? 'N/A' }}</p>
            <p><strong>Capacity:</strong> {{ $gymClass->capacity ?? 'N/A' }} members</p>
            <p><strong>Description:</strong> {{ $gymClass->description ?? 'No description available.' }}</p>
        </div>

        <div class="card">
            <h3>Booking Overview</h3>
            <p><strong>Total Bookings:</strong> {{ $bookingsCount }}</p>
            <p><strong>Available Spots:</strong> {{ $availableSpots }}</p>
            <p>
                <strong>Status:</strong>
                <span style="color: {{ $availableSpots > 0 ? '#5fd68f' : '#ff5555' }}; font-weight:600;">
                    {{ $availableSpots > 0 ? 'Open for Booking' : 'Full' }}
                </span>
            </p>
        </div>
    </div>

    @if ($gymClass->bookings->count())
        <div class="card" style="margin-top:1.5rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
                <h3 style="margin:0;">Members Booked</h3>
                <span style="color:#aaa;">{{ $bookingsCount }} total</span>
            </div>

            <ul style="list-style:none; padding:0; margin-top:1rem;">
                @foreach ($gymClass->bookings as $booking)
                    <li style="padding:12px 0; border-bottom:1px solid #2b2b2b;">
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
                            <div>
                                <strong>{{ $booking->member->user->name ?? 'N/A' }}</strong>
                                <div style="color:#a9a89d; font-size:0.9rem;">
                                    {{ $booking->created_at->format('M d, Y') }}
                                </div>
                            </div>

                            <span style="color: {{ $booking->status === 'confirmed' ? '#5fd68f' : '#ffd700' }}; font-weight:600;">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @auth
        @if(auth()->user()->isTrainer() || auth()->user()->isAdmin())
            <div style="margin-top:1.5rem;">
                <form action="{{ route('classes.destroy', $gymClass) }}" method="POST" class="delete-form">
                    @csrf
                    @method('DELETE')

                    <button type="button" class="btn btn-danger btn-delete">
                        Delete Class
                    </button>
                </form>
            </div>
        @endif
    @endauth

@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {
                const form = this.closest('form');

                window.showModal({
                    type: 'warning',
                    title: 'Delete Class?',
                    message: 'This class will be removed permanently.',
                    confirmText: 'Yes, Delete',
                    onConfirm: () => form.submit()
                });
            });
        });
    </script>
@endpush