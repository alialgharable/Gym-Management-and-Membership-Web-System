@extends('layouts.app')

@section('title', 'Create Trainer')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Create New Trainer</h1>
            <p class="section-subtitle">Add a new trainer to the system</p>
        </div>
        <a href="{{ route('trainers.index') }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('trainers.store') }}" method="POST">
            @csrf

            <div class="field-group">
                <label class="field-label">User <span style="color: #ff5555;">*</span></label>
                <select name="user_id" class="field-select" required>
                    <option value="">Select a user...</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Specialization</label>
                <select name="specialty" class="field-select" required>
                    <option value="">Select specialty...</option>
                    @foreach ($specialties as $value => $label)
                        <option value="{{ $value }}" @selected(old('specialty') == $value)>
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
                <textarea name="bio" class="field-input" rows="4" style="resize: vertical;" placeholder="Trainer bio and experience">{{ old('bio') }}</textarea>
                @error('bio')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Create Trainer</button>
                <a href="{{ route('trainers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
