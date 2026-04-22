@extends('layouts.app')

@section('title', 'Program Details')

@section('content')
    @php
        $isOwnerTrainer = auth()->user()->isTrainer() && auth()->user()->trainer && auth()->user()->trainer->id === $program->trainer_id;
        $canManage = auth()->user()->isAdmin() || $isOwnerTrainer;
    @endphp

    <div class="page-header">
        <div>
            <h1 class="section-title">{{ $program->title }}</h1>
            <p class="section-subtitle">Specialty-based member program assigned by coach.</p>
        </div>

        <div class="actions" style="display:flex; gap:8px;">
            <a href="{{ route('programs.index') }}" class="btn btn-secondary">Back</a>
            @if($canManage)
                <a href="{{ route('programs.edit', $program) }}" class="btn btn-secondary">Edit</a>
                <form action="{{ route('programs.destroy', $program) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this program?')">Delete</button>
                </form>
            @endif
        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
        <div class="card">
            <h3>Program Info</h3>
            <p><strong>Coach:</strong> {{ $program->trainer->user->name ?? 'N/A' }}</p>
            <p><strong>Member:</strong> {{ $program->member->user->name ?? 'N/A' }}</p>
            <p><strong>Specialty:</strong> {{ $program->trainer ? $program->trainer->specialtyLabel() : ucfirst(str_replace('_', ' ', $program->specialty)) }}</p>
            <p><strong>Duration:</strong> {{ $program->duration_weeks }} week(s)</p>
            <p><strong>Created:</strong> {{ $program->created_at?->format('Y-m-d') }}</p>
        </div>

        <div class="card">
            <h3>Goal</h3>
            <p>{{ $program->goal ?: 'No goal provided.' }}</p>
        </div>
    </div>

    <div class="card" style="margin-top:1.5rem;">
        <h3>Program Notes</h3>
        <p style="white-space:pre-wrap;">{{ $program->notes ?: 'No notes provided.' }}</p>
    </div>
@endsection
