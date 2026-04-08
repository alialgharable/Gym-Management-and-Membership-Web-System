@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
    @php
        $user = $member->user;
        $profileImage = $user && $user->profile_picture
            ? asset('storage/' . $user->profile_picture)
            : asset('images/default-avatar.png');
    @endphp

    <div class="page-header">
        <div>
            <h1 class="section-title">Edit Profile</h1>
            <p class="section-subtitle">Update your personal information and profile picture</p>
        </div>
        <a href="{{ route('members.show', $member) }}" class="btn btn-secondary">← Back to Profile</a>
    </div>

    <div class="card" style="max-width: 780px; margin: 0 auto;">
        <form action="{{ route('members.update', $member) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid #2b2b2b;">
                <img
                    src="{{ $profileImage }}"
                    alt="Profile Picture"
                    class="profile-avatar"
                    style="width: 96px; height: 96px; border-radius: 50%; object-fit: cover; border: 3px solid #ffd54a;"
                    onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';">

                <div style="flex: 1; min-width: 220px;">
                    <h3 style="margin: 0 0 0.35rem;">Profile Picture</h3>
                    <p style="margin: 0 0 0.85rem; color: #a9a89d; font-size: 0.95rem;">
                        Choose a profile photo that will appear on your account.
                    </p>

                    <input type="file" name="profile_picture" class="field-input" accept="image/*">
                    @error('profile_picture')
                        <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <h3 style="margin-bottom: 1rem;">Personal Information</h3>
            </div>

            <div class="field-group">
                <label class="field-label" for="name">Full Name</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name ?? '') }}"
                    class="field-input"
                    required>
                @error('name')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label" for="email">Email Address</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email ?? '') }}"
                    class="field-input"
                    required>
                @error('email')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Member Since</label>
                <input
                    type="text"
                    value="{{ $member->created_at->format('M d, Y') }}"
                    class="field-input"
                    disabled>
            </div>

            <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #2b2b2b;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('members.show', $member) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection