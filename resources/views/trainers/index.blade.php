@extends('layouts.app')

@section('title', 'Trainers')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Trainers</h1>
            <p class="section-subtitle">Manage gym trainers and their classes</p>
        </div>
    </div>

    @if ($trainers->count())
        <div class="card-grid">
            @foreach ($trainers as $trainer)
                @php
                    $trainerUser = $trainer->user;
                    $profileImage = $trainerUser && $trainerUser->profile_picture
                        ? asset('storage/' . $trainerUser->profile_picture)
                        : asset('images/default-avatar.png');
                @endphp

                <div class="card">
                    <div style="display: flex; align-items: center; gap: 0.9rem; margin-bottom: 1rem;">
                        <img
                            src="{{ $profileImage }}"
                            alt="{{ $trainerUser->name ?? 'Trainer' }}"
                            style="width: 56px; height: 56px; border-radius: 50%; object-fit: cover; border: 2px solid #ffd54a;"
                            onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';">

                        <div>
                            <h3 style="margin: 0;">{{ $trainerUser->name ?? 'N/A' }}</h3>
                            <p style="margin: 0.2rem 0 0; font-size: 0.9rem; color: #a9a89d;">
                                Trainer Profile
                            </p>
                        </div>
                    </div>

                    <p><strong>Email:</strong> {{ $trainerUser->email ?? 'N/A' }}</p>
                    <p><strong>Specialization:</strong> {{ $trainer->specialty ?? 'N/A' }}</p>
                    <p style="font-size: 0.9rem; color: #a9a89d;">
                        Classes: {{ $trainer->gymClasses->count() }} | Reviews: {{ $trainer->reviews->count() }}
                    </p>

                    <div class="actions" style="margin-top: 1rem;">
                        <a href="{{ route('trainers.show', $trainer) }}" class="btn btn-secondary">View</a>

                        @auth
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('trainers.edit', $trainer) }}" class="btn btn-secondary">Edit</a>
                                <form action="{{ route('trainers.destroy', $trainer) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        @endauth
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