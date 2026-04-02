@extends('layouts.app')

@section('title', 'Edit Booking')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Edit Booking</h1>
            <p class="section-subtitle">Update booking details</p>
        </div>
        <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('bookings.update', $booking) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field-group">
                <label class="field-label">Member <span style="color: #ff5555;">*</span></label>
                <select name="member_id" class="field-select" required>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}" @selected($booking->member_id == $member->id)>
                            {{ $member->user->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
                @error('member_id')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Class <span style="color: #ff5555;">*</span></label>
                <select name="class_id" class="field-select" required>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" @selected($booking->class_id == $class->id)>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
                @error('class_id')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Status</label>
                <select name="status" class="field-select">
                    <option value="confirmed" @selected($booking->status == 'confirmed')>Confirmed</option>
                    <option value="pending" @selected($booking->status == 'pending')>Pending</option>
                    <option value="cancelled" @selected($booking->status == 'cancelled')>Cancelled</option>
                </select>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
