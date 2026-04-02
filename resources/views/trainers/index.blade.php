@extends('layouts.app')

@section('title', 'Trainers')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Trainers</h1>
            <p class="section-subtitle">Manage gym trainers and their classes</p>
        </div>
        <a href="{{ route('trainers.create') }}" class="btn btn-primary">+ Add Trainer</a>
    </div>

    @if ($trainers->count())
        <div class="card-grid">
            @foreach ($trainers as $trainer)
                <div class="card">
                    <h3>{{ $trainer->user->name ?? 'N/A' }}</h3>
                    <p><strong>Email:</strong> {{ $trainer->user->email ?? 'N/A' }}</p>
                    <p><strong>Specialization:</strong> {{ $trainer->specialty ?? 'N/A' }}</p>
                    <p style="font-size: 0.9rem; color: #a9a89d;">Classes: {{ $trainer->gymClasses->count() }} | Reviews: {{ $trainer->reviews->count() }}</p>
                    <div class="actions" style="margin-top: 1rem;">
                        <a href="{{ route('trainers.show', $trainer) }}" class="btn btn-secondary">View</a>
                        <a href="{{ route('trainers.edit', $trainer) }}" class="btn btn-secondary">Edit</a>
                        <form action="{{ route('trainers.destroy', $trainer) }}" method="POST" style="display:inline;">
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
            No trainers found. <a href="{{ route('trainers.create') }}" style="color: #ffd700; font-weight: bold;">Add one now</a>
        </div>
    @endif
@endsection
