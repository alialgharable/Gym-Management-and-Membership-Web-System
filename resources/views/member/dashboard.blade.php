@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')

    @if ($member)
        @php
            $activeSub = $member->subscription()->where('status', 'active')->first();
            $planName = $activeSub && $activeSub->plan ? $activeSub->plan->name : null;
            $totalBookings = $member->bookings->count();
            $confirmedBookings = $member->bookings->where('status', 'confirmed')->count();
        @endphp

        <div class="page-header">
            <div>
                <h1 class="section-title">My Dashboard</h1>
                <p class="section-subtitle">Welcome back, {{ $member->user->name ?? 'Member' }}</p>
            </div>
        </div>

        <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">

            <div class="card" style="background: linear-gradient(135deg, #2e7d32, #1b5e20);">
                <h3 style="color:#fff;">Subscription</h3>
                @if ($activeSub && $planName)
                    <p style="color:#a8d5a8; margin:6px 0;"><strong>{{ $planName }}</strong></p>
                    <p style="color:#a8d5a8; font-size:0.9rem;">
                        Valid until {{ $activeSub->end_date->format('M d, Y') }}
                    </p>
                @else
                    <p style="color:#ff5555;">No active subscription</p>
                @endif
            </div>

            <div class="card" style="background: linear-gradient(135deg, #1976d2, #0d47a1);">
                <h3 style="color:#fff;">Bookings</h3>
                <p style="font-size:2.5rem; font-weight:700; color:#fff;">{{ $totalBookings }}</p>
                <p style="color:#90caf9; font-size:0.9rem;">Total classes booked</p>
            </div>

            <div class="card" style="background: linear-gradient(135deg, #f57c00, #bf360c);">
                <h3 style="color:#fff;">Confirmed</h3>
                <p style="font-size:2.5rem; font-weight:700; color:#fff;">{{ $confirmedBookings }}</p>
                <p style="color:#ffb74d; font-size:0.9rem;">Active bookings</p>
            </div>

            <div class="card" style="background: linear-gradient(135deg, #c2185b, #880e4f);">
                <h3 style="color:#fff;">Member Since</h3>
                <p style="color:#f48fb1;">
                    {{ $member->created_at->format('M d, Y') }}
                </p>
                <p style="color:#f48fb1; font-size:0.9rem;">
                    (
                    @if($member->created_at->diffInDays() < 30)
                        {{ $member->created_at->diffInDays() }} days
                    @else
                        {{ $member->created_at->diffInMonths() }} months
                    @endif
                    )
                </p>
            </div>

        </div>

        <div class="card" style="margin-top:1.5rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                <h3 style="margin:0;">My Bookings</h3>
                <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                    Book Class
                </a>
            </div>

            @if ($member->bookings->count())
                <div style="overflow-x:auto; margin-top:1rem;">
                    <table>
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Trainer</th>
                                <th>Schedule</th>
                                <th>Status</th>
                                <th>Booked</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($member->bookings as $booking)
                                @php
                                    $className = optional($booking->gymClass)->name ?? 'N/A';
                                    $trainerName = optional(optional($booking->gymClass)->trainer)->user->name ?? 'N/A';
                                    $schedule = optional($booking->gymClass)->schedule;
                                @endphp
                                <tr>
                                    <td>{{ $className }}</td>
                                    <td>{{ $trainerName }}</td>
                                    <td>
                                        {{ $schedule ? $schedule->format('M d, Y H:i') : 'N/A' }}
                                    </td>

                                    <td>
                                        <span style="color:
                                                            {{ $booking->status === 'confirmed' ? '#5fd68f' :
                                ($booking->status === 'cancelled' ? '#ff5555' : '#ffd700') }};">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>

                                    <td>{{ $booking->created_at->format('M d, Y') }}</td>

                                    <td>
                                        @if($booking->status === 'confirmed')
                                            <form action="{{ route('bookings.update', $booking) }}" method="POST" class="cancel-form">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="cancelled">

                                                <button type="button" class="btn btn-danger btn-cancel" style="padding:0.4rem 0.8rem;">
                                                    Cancel
                                                </button>
                                            </form>
                                        @else
                                            <span style="color:#777;">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="color:#aaa; margin-top:1rem;">
                    No bookings yet.
                    <a href="{{ route('bookings.create') }}" style="color:#f7d34a; font-weight:bold;">
                        Book a class
                    </a>
                </p>
            @endif
        </div>

    @else
        <div class="card" style="text-align:center; padding:40px;">
            <h3 style="color:#ff5555;">Profile Not Found</h3>
            <p style="color:#aaa;">Please contact support.</p>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.btn-cancel').forEach(button => {
            button.addEventListener('click', function () {
                const form = this.closest('form');

                window.showModal({
                    type: 'warning',
                    title: 'Cancel Booking?',
                    message: 'This booking will be cancelled.',
                    confirmText: 'Yes, Cancel',
                    onConfirm: () => form.submit()
                });
            });
        });
    </script>
@endpush