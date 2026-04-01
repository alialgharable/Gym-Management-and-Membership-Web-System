@extends('layouts.app')

@section('content')

    <h1>All Bookings</h1>

    @foreach($bookings as $booking)
        <div style="border:1px solid black; margin:10px; padding:10px;">
            <p><strong>Class:</strong> {{ $booking->gymClass->name ?? 'N/A' }}</p>
            <p><strong>Status:</strong> {{ $booking->status }}</p>
        </div>
    @endforeach

@endsection