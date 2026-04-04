@extends('layouts.app')

@section('title', 'Edit Member')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Edit Member</h1>
            <p class="section-subtitle">Update member information</p>
        </div>
        <a href="{{ route('members.show', $member) }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('members.update', $member) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field-group">
                <label class="field-label">Member Name</label>
                <input type="text" name="name" value="{{ old('name', $member->user->name ?? '') }}" class="field-input" required>
                @error('name')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Member Email</label>
                <input type="email" name="email" value="{{ old('email', $member->user->email ?? '') }}" class="field-input" required>
                @error('email')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Member Since</label>
                <input type="text" value="{{ $member->created_at->format('M d, Y') }}" class="field-input" disabled>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('members.show', $member) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
