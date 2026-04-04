@extends('layouts.app')

@section('title', 'Create Booking')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Create New Booking</h1>
            <p class="section-subtitle">Book a member for a gym class</p>
        </div>
        <a href="{{ route('bookings.index') }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('bookings.store') }}" method="POST">
            @csrf

            @if ($isMember)
                <div class="field-group">
                    <label class="field-label">Member</label>
                    <div class="field-input" style="padding: 0.75rem; background-color: #fafafa; border: 1px solid #ddd; border-radius: 4px; color: #333;">
                        {{ auth()->user()->name }}
                    </div>
                </div>
            @else
                <div class="field-group">
                    <label class="field-label">Member <span style="color: #ff5555;">*</span></label>
                    <select name="member_id" class="field-select" required>
                        <option value="">Select a member...</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>
                                {{ $member->user->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    @error('member_id')
                        <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                    @enderror
                </div>
            @endif

            <div class="field-group">
                <label class="field-label">Class <span style="color: #ff5555;">*</span></label>
                <select name="class_id" class="field-select" required>
                    <option value="">Select a class...</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" @selected(old('class_id') == $class->id)>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
                @error('class_id')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Create Booking</button>
                <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection