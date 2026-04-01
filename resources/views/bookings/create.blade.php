@extends('layouts.app')

@section('content')

    <h1>Create Booking</h1>

    <form action="{{ route('bookings.store') }}" method="POST">
        @csrf

        <!-- Member -->
        <label>Member:</label>
        <select name="member_id">
            @foreach($members as $member)
                <option value="{{ $member->id }}">
                    Member #{{ $member->id }}
                </option>
            @endforeach
        </select>

        <br><br>

        <!-- Class -->
        <label>Class:</label>
        <select name="class_id">
            @foreach($classes as $class)
                <option value="{{ $class->id }}">
                    {{ $class->name }}
                </option>
            @endforeach
        </select>

        <br><br>

        <button type="submit">Book</button>

    </form>

@endsection