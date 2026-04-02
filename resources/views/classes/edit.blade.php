@extends('layouts.app')

@section('title', 'Edit Class')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Edit Class</h1>
            <p class="section-subtitle">Update class information</p>
        </div>
        <a href="{{ route('classes.show', $gymClass) }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('classes.update', $gymClass) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field-group">
                <label class="field-label">Class Name <span style="color: #ff5555;">*</span></label>
                <input type="text" name="name" class="field-input" value="{{ old('name', $gymClass->name) }}" required>
                @error('name')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Trainer <span style="color: #ff5555;">*</span></label>
                <select name="trainer_id" class="field-select" required>
                    @foreach ($trainers as $trainer)
                        <option value="{{ $trainer->id }}" @selected($gymClass->trainer_id == $trainer->id)>
                            {{ $trainer->user->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
                @error('trainer_id')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Schedule</label>
                <input type="text" name="schedule" class="field-input" value="{{ old('schedule', $gymClass->schedule) }}" placeholder="e.g., Monday & Wednesday 10:00 AM">
                @error('schedule')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Capacity</label>
                <input type="number" name="capacity" class="field-input" value="{{ old('capacity', $gymClass->capacity) }}" min="1">
                @error('capacity')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Description</label>
                <textarea name="description" class="field-input" rows="4" style="resize: vertical;">{{ old('description', $gymClass->description) }}</textarea>
                @error('description')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('classes.show', $gymClass) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
