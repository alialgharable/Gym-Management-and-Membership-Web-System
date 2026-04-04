@extends('layouts.app')

@section('title', 'Gym Classes')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Gym Classes</h1>
            <p class="section-subtitle">Manage gym class schedules and trainers</p>
        </div>
        @if(auth()->check() && (auth()->user()->isTrainer() || auth()->user()->isAdmin()))
            <a href="{{ route('classes.create') }}" class="btn btn-primary">+ New Class</a>
        @endif
    </div>

    @if ($classes->count())
        <div class="card-grid">
            @foreach ($classes as $class)
                <div class="card">
                    <h3>{{ $class->name }}</h3>
                    <p><strong>Trainer:</strong> {{ $class->trainer->user->name ?? 'N/A' }}</p>
                    <p><strong>Schedule:</strong> {{ $class->schedule ?? 'N/A' }}</p>
                    <p><strong>Capacity:</strong> {{ $class->capacity ?? 'N/A' }} members</p>
                    <p style="font-size: 0.9rem; color: #a9a89d;">Bookings: {{ $class->bookings->count() }}</p>
                    <div class="actions" style="margin-top: 1rem;">
                        <a href="{{ route('classes.show', $class) }}" class="btn btn-secondary">View</a>
                        <a href="{{ route('classes.edit', $class) }}" class="btn btn-secondary">Edit</a>
                        <form action="{{ route('classes.destroy', $class) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning">
            No classes found. <a href="{{ route('classes.create') }}" style="color: #ffd700; font-weight: bold;">Create one now</a>
        </div>
    @endif
@endsection
