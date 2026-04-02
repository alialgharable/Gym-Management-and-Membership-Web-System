@extends('layouts.app')

@section('title', 'Edit Trainer')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Edit Trainer</h1>
            <p class="section-subtitle">Update trainer information</p>
        </div>
        <a href="{{ route('trainers.show', $trainer) }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('trainers.update', $trainer) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field-group">
                <label class="field-label">Trainer Name</label>
                <input type="text" value="{{ $trainer->user->name ?? '' }}" class="field-input" disabled>
                <small style="color: #a9a89d;">User information cannot be edited here</small>
            </div>

            <div class="field-group">
                <label class="field-label">Specialization</label>
                <input type="text" name="specialty" class="field-input" value="{{ old('specialty', $trainer->specialty) }}" placeholder="e.g., CrossFit, Yoga, Boxing">
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
                <a href="{{ route('trainers.show', $trainer) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
