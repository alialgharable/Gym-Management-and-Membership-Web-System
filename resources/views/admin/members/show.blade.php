@extends('layouts.app')

@section('title', 'Member Details')

@section('content')
    @php
        $user = $member->user;
        $profileImage = $user && $user->profile_picture
            ? asset('storage/' . $user->profile_picture)
            : asset('images/default-avatar.png');

        $activeSub = $member->subscription()->where('status', 'active')->first();
    @endphp

    <div class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <img src="{{ $profileImage }}" alt="{{ $user->name ?? 'Member' }}"
                    style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #ffd54a; box-shadow: 0 6px 18px rgba(0,0,0,0.25);">

                <div>
                    <h1 class="section-title" style="margin-bottom: 0.35rem;">
                        {{ $user->name ?? 'Member' }}
                    </h1>
                    <p class="section-subtitle" style="margin-bottom: 0.35rem;">
                        Member ID: #{{ $member->id }}
                    </p>
                    <p style="margin: 0; color: #a9a89d;">
                        {{ $user->email ?? 'N/A' }}
                    </p>
                    <p style="margin: 0.35rem 0 0; color: #a9a89d; font-size: 0.95rem;">
                        Member since {{ $member->created_at->format('M d, Y') }}
                    </p>
                </div>
            </div>

            <div class="actions" style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                @if(auth()->user()->isMember())
                    <a href="{{ route('member.dashboard') }}" class="btn btn-secondary">← Back</a>
                @elseif(auth()->user()->isAdmin())
                    <a href="{{ route('members.index') }}" class="btn btn-secondary">← Back</a>
                @endif

                @auth
                    @if(auth()->id() === $member->user_id)
                        <a href="{{ route('members.edit', $member) }}" class="btn btn-primary">Edit Profile</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: 1fr 1fr;">
        <div class="card">
            <h3>Personal Information</h3>
            <p><strong>Name:</strong> {{ $user->name ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $user->email ?? 'N/A' }}</p>
            <p><strong>Member Since:</strong> {{ $member->created_at->format('M d, Y') }}</p>
        </div>

        <div class="card">
            <h3>Subscription Status</h3>
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
            <ul style="list-style: none; padding: 0; margin: 0;">
                @foreach ($member->bookings as $booking)
                    <li style="padding: 10px 0; border-bottom: 1px solid #2b2b2b;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
                            <div>
                                <strong>{{ $booking->gymClass->name ?? 'N/A' }}</strong>
                                <div style="color: #a9a89d; font-size: 0.9rem;">
                                    {{ $booking->created_at->format('M d, Y') }}
                                </div>
                            </div>

                            <span style="color: {{ $booking->status === 'confirmed' ? '#5fd68f' : '#ffd700' }}; font-weight: 600;">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <p style="color: #a9a89d;">No bookings yet</p>
        @endif
    </div>

    @auth
        @if(auth()->id() === $member->user_id)
            <div style="margin-top: 1.5rem;">
                <form action="{{ route('members.destroy', $member) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('This will permanently delete this member. Are you sure?')">
                        Cancel Subscription
                    </button>
                </form>
            </div>
        @endif
    @endauth
@endsection