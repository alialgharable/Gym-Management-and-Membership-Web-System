@extends('layouts.app')

@section('title', 'Edit Trainer')

@section('content')
    @php
        $user = $trainer->user;
        $profileImage = $user && $user->profile_picture
            ? asset('storage/' . $user->profile_picture)
            : asset('images/default-avatar.png');
    @endphp

    <div class="page-header">
        <div>
            <h1 class="section-title">Edit Trainer</h1>
            <p class="section-subtitle">Update trainer information</p>
        </div>
        @if(auth()->check() && auth()->user()->trainer && auth()->user()->trainer->id === $trainer->id)
            <a href="{{ route('trainer.dashboard') }}" class="btn btn-secondary">← Back to Dashboard</a>
        @else
            <a href="{{ route('trainers.show', $trainer) }}" class="btn btn-secondary">← Back</a>
        @endif
    </div>

    <div class="card" style="max-width: 780px; margin: 0 auto;">
        <form action="{{ route('trainers.update', $trainer) }}" method="POST" enctype="multipart/form-data">
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
                        Upload a profile photo for this trainer account.
                    </p>

                    <input type="file" name="profile_picture" class="field-input" accept="image/*">
                    @error('profile_picture')
                        <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="field-group">
                <label class="field-label">Trainer Name</label>
                <input type="text" value="{{ $trainer->user->name ?? '' }}" class="field-input" disabled>
                <small style="color: #a9a89d;">User information cannot be edited here</small>
            </div>

            <div class="field-group">
                <label class="field-label">Specialization</label>
                <select name="specialty" class="field-select" required>
                    @foreach ($specialties as $value => $label)
                        <option value="{{ $value }}" @selected(old('specialty', $trainer->specialty) == $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('specialty')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Bio</label>
                <textarea name="bio" class="field-input" rows="4" style="resize: vertical;">{{ old('bio', $trainer->bio) }}</textarea>
                @error('bio')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                @if(auth()->check() && auth()->user()->trainer && auth()->user()->trainer->id === $trainer->id)
                    <a href="{{ route('trainer.dashboard') }}" class="btn btn-secondary">Cancel</a>
                @else
                    <a href="{{ route('trainers.show', $trainer) }}" class="btn btn-secondary">Cancel</a>
                @endif
            </div>
        </form>
    </div>
@endsection
