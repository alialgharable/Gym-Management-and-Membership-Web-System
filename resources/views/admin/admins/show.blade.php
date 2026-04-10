@extends('layouts.app')

@section('title', 'Admin Details')

@section('content')
    @php
        $user = $admin->user;
        $profileImage = $user && $user->profile_picture
            ? asset('storage/' . $user->profile_picture)
            : asset('images/default-avatar.png');
    @endphp

    <div class="card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <img src="{{ $profileImage }}" alt="{{ $user->name ?? 'Administrator' }}"
                    style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #ffd54a; box-shadow: 0 6px 18px rgba(0,0,0,0.25);">

                <div>
                    <h1 class="section-title" style="margin-bottom: 0.35rem;">
                        {{ $user->name ?? 'Administrator' }}
                    </h1>
                    <p class="section-subtitle" style="margin-bottom: 0.35rem;">
                        Admin ID: #{{ $admin->id }}
                    </p>
                    <p style="margin: 0; color: #a9a89d;">
                        {{ $user->email ?? 'N/A' }}
                    </p>
                    <p style="margin: 0.35rem 0 0; color: #a9a89d; font-size: 0.95rem;">
                        Admin since {{ $admin->created_at->format('M d, Y') }}
                    </p>
                </div>
            </div>

            <div class="actions" style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← Back</a>
                <a href="{{ route('admins.edit', $admin) }}" class="btn btn-primary">Edit Profile</a>
            </div>
        </div>
    </div>

    {{-- Full width now (no grid needed) --}}
    <div class="card">
        <h3>Administrator Information</h3>
        <p><strong>Name:</strong> {{ $user->name ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $user->email ?? 'N/A' }}</p>
        <p><strong>Admin Since:</strong> {{ $admin->created_at->format('M d, Y') }}</p>
    </div>

    <div class="card" style="margin-top: 1.5rem;">
        <h3>Admin Role Summary</h3>
        <p style="color: #a9a89d; margin-bottom: 0.75rem;">
            This administrator has access to manage members, trainers, bookings, classes, and overall system settings.
        </p>
        <p><strong>Status:</strong> 
            <span style="color: #5fd68f; font-weight: 600;">Active Administrator</span>
        </p>
    </div>

    <div style="margin-top: 1.5rem;">
        <form action="{{ route('admins.destroy', $admin) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger"
                onclick="return confirm('This will permanently delete this admin. Are you sure?')">
                Delete Admin
            </button>
        </form>
    </div>
@endsection