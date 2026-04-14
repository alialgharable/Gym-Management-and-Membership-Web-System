@extends('layouts.app')

@section('title', 'Create Class')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="section-title">Create New Class</h1>
            <p class="section-subtitle">Add a new gym class</p>
        </div>
        <a href="{{ auth()->user()->isTrainer() ? route('trainer.dashboard') : route('classes.index') }}"
            class="btn btn-secondary">
            Back
        </a>
    </div>

    <div class="card" style="max-width:700px; margin:0 auto;">
        <form action="{{ route('classes.store') }}" method="POST">
            @csrf

            <div class="field-group">
                <label class="field-label">Class Name</label>
                <input type="text" name="name" class="field-input" value="{{ old('name') }}" required>
                @error('name')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            @if ($isTrainer)
                <div class="field-group">
                    <label class="field-label">Trainer</label>
                    <div class="field-input"
                        style="padding:0.75rem; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08);">
                        {{ auth()->user()->trainer->user->name ?? 'Your Profile' }}
                    </div>
                </div>
            @else
                <div class="field-group">
                    <label class="field-label">Trainer</label>
                    <select name="trainer_id" class="field-select" required>
                        <option value="">Select a trainer...</option>
                        @foreach ($trainers as $trainer)
                            <option value="{{ $trainer->id }}" @selected(old('trainer_id') == $trainer->id)>
                                {{ $trainer->user->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    @error('trainer_id')
                        <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                    @enderror
                </div>
            @endif

            <div class="field-group">
                <label class="field-label">Schedule</label>
                <input type="datetime-local" name="schedule" class="field-input" value="{{ old('schedule') }}">
                @error('schedule')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Category</label>
                <select name="category" class="field-select" required>
                    @if ($isTrainer)
                        @php
                            $trainerSpecialty = auth()->user()->trainer->specialty;
                            $trainerSpecialtyLabel = $categories[$trainerSpecialty] ?? 'My Specialty';
                        @endphp
                        <option value="{{ $trainerSpecialty }}" selected>
                            {{ $trainerSpecialtyLabel }}
                        </option>
                    @else
                        <option value="">Select a category...</option>
                        @foreach ($categories as $value => $label)
                            <option value="{{ $value }}" @selected(old('category') == $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    @endif
                </select>

                <small style="color:#888; display:block; margin-top:4px;">
                    @if ($isTrainer)
                        You can only create classes in your specialty.
                    @else
                        Room is assigned automatically based on category.
                    @endif
                </small>

                @error('category')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label" style="display:flex; align-items:center; gap:0.6rem;">
                    <input type="checkbox" name="create_full_month" value="1" {{ old('create_full_month') ? 'checked' : '' }}>
                    Create full month schedule
                </label>
                <small style="color:#888; display:block; margin-top:4px;">
                    Repeats every 7 days until the end of the month.
                </small>
                @error('create_full_month')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Capacity</label>
                <input type="number" name="capacity" class="field-input" value="{{ old('capacity') }}" min="1" max="30">
                @error('capacity')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Description</label>
                <textarea name="description" class="field-input" rows="4"
                    style="resize:vertical;">{{ old('description') }}</textarea>
                @error('description')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:2rem;">
                <button type="submit" class="btn btn-primary">Create Class</button>
                <a href="{{ route('classes.index') }}" class="btn btn-secondary">Cancel</a>
            </div>

        </form>
    </div>

@endsection