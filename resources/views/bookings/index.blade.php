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

    <div style="margin-bottom:1rem; display:flex; gap:8px; align-items:center;">
        <form id="bookings-filters" method="GET" action="{{ route('bookings.index') }}"
            style="display:flex; gap:8px; align-items:center;">
            <input type="text" name="search" placeholder="Search bookings (member or class)..."
                value="{{ request('search') }}"
                style="padding:8px 10px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); background:transparent; color:inherit;">

            <select name="status"
                style="padding:8px 10px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); background:transparent; color:inherit;">
                <option value="">Any status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <select name="class_id"
                style="padding:8px 10px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); background:transparent; color:inherit;">
                <option value="">All classes</option>
                @foreach(\App\Models\GymClass::all() as $c)
                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>

            <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Reset</a>
        </form>
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

                                    @if(auth()->check() && auth()->user()->isMember() && $booking->status !== 'cancelled')
                                        <form action="{{ route('bookings.destroy', $booking) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-warning"
                                                onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif

                                    @if(auth()->check() && (auth()->user()->isTrainer() || auth()->user()->isAdmin()))
                                        <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-secondary">Edit</a>

                                        <form action="{{ route('bookings.destroy', $booking) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-warning">
            No bookings found. <a href="{{ route('bookings.create') }}" style="color: #ffd700; font-weight: bold;">Create one
                now</a>
        </div>
    @endif
    @push('scripts')
        <script>
            (function () {
                const form = document.getElementById('bookings-filters');
                if (!form) return;

                let timer;
                const submit = () => form.submit();

                const search = form.querySelector('input[name="search"]');
                if (search) {
                    search.addEventListener('input', function () {
                        clearTimeout(timer);
                        timer = setTimeout(submit, 500);
                    });
                }

                form.querySelectorAll('select').forEach(s => s.addEventListener('change', submit));
            })();
        </script>
    @endpush
@endsection