@extends('layouts.app')

@section('title', 'Create Booking')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Create a New Booking</h1>
            <p class="section-subtitle">Select your member profile and the class you want to book.</p>
        </div>
    </div>

    <form action="{{ route('bookings.store') }}" method="POST" class="grid-stack">
        @csrf

        <div class="field-group">
            <label class="field-label" for="member_id">Member</label>
            <select id="member_id" name="member_id" class="field-select" required>
                @foreach($members as $member)
                    <option value="{{ $member->id }}">{{ $member->user->name ?? 'Member #' . $member->id }}</option>
                @endforeach
            </select>
        </div>

        <div class="field-group">
            <label class="field-label" for="class_id">Class</label>
            <select id="class_id" name="class_id" class="field-select" required>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="field-group">
            <button type="submit" class="field-button">Book Class</button>
        </div>
    </form>
@endsection