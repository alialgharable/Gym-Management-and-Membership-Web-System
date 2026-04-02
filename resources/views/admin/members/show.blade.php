@extends('layouts.app')

@section('title', 'Member Details')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">{{ $member->user->name ?? 'Member' }}</h1>
            <p class="section-subtitle">Member ID: #{{ $member->id }}</p>
        </div>
        <div class="actions">
            <a href="{{ route('members.index') }}" class="btn btn-secondary">← Back</a>
            <a href="{{ route('members.edit', $member) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: 1fr 1fr;">
        <div class="card">
            <h3>Personal Information</h3>
            <p><strong>Name:</strong> {{ $member->user->name ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $member->user->email ?? 'N/A' }}</p>
            <p><strong>Member Since:</strong> {{ $member->created_at->format('M d, Y') }}</p>
        </div>

        <div class="card">
            <h3>Subscription Status</h3>
            @php
                $activeSub = $member->subscription()->where('status', 'active')->first();
            @endphp
            @if ($activeSub)
                <p><strong>Plan:</strong> {{ $activeSub->plan->name ?? 'N/A' }}</p>
                <p><strong>Price:</strong> ${{ $activeSub->plan->price ?? 'N/A' }}</p>
                <p><strong>Valid Until:</strong> {{ $activeSub->end_date->format('M d, Y') }}</p>
            @else
                <p style="color: #ff5555;">No active subscription</p>
            @endif
        </div>
    </div>

    <div class="card" style="margin-top: 1.5rem;">
        <h3>Bookings ({{ $member->bookings->count() }})</h3>
        @if ($member->bookings->count())
            <ul style="list-style: none; padding: 0;">
                @foreach ($member->bookings as $booking)
                    <li style="padding: 10px 0; border-bottom: 1px solid #2b2b2b;">
                        <strong>{{ $booking->gymClass->name ?? 'N/A' }}</strong>
                        <span style="color: #a9a89d; font-size: 0.9rem;">{{ $booking->created_at->format('M d, Y') }}</span>
                        <span style="color: {{ $booking->status === 'confirmed' ? '#5fd68f' : '#ffd700' }};">{{ ucfirst($booking->status) }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p style="color: #a9a89d;">No bookings yet</p>
        @endif
    </div>

    <div style="margin-top: 1.5rem;">
        <form action="{{ route('members.destroy', $member) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('This will permanently delete this member. Are you sure?')">Delete Member</button>
        </form>
    </div>
@endsection
