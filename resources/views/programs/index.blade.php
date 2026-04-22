@extends('layouts.app')

@section('title', 'Programs')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Programs</h1>
            <p class="section-subtitle">Training programs created by coaches and assigned to members.</p>
        </div>

        @if(auth()->user()->isAdmin() || auth()->user()->isTrainer())
            <a href="{{ route('programs.create') }}" class="btn btn-primary">+ Create Program</a>
        @endif
    </div>

    @if($programs->isEmpty())
        <div class="card">
            <h3>No programs found</h3>
            <p>
                @if(auth()->user()->isMember())
                    Your coach has not assigned a program yet.
                @else
                    Start by creating your first program.
                @endif
            </p>
        </div>
    @else
        <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
            @foreach($programs as $program)
                @php
                    $isOwnerTrainer = auth()->user()->isTrainer() && auth()->user()->trainer && auth()->user()->trainer->id === $program->trainer_id;
                    $canManage = auth()->user()->isAdmin() || $isOwnerTrainer;
                @endphp
                <div class="card" style="display:flex; flex-direction:column; justify-content:space-between;">
                    <div>
                        <h3>{{ $program->title }}</h3>
                        <p><strong>Member:</strong> {{ $program->member->user->name ?? 'N/A' }}</p>
                        <p><strong>Coach:</strong> {{ $program->trainer->user->name ?? 'N/A' }}</p>
                        <p><strong>Specialty:</strong> {{ $program->trainer ? $program->trainer->specialtyLabel() : ucfirst(str_replace('_', ' ', $program->specialty)) }}</p>
                        <p><strong>Duration:</strong> {{ $program->duration_weeks }} week(s)</p>
                    </div>

                    <div class="actions" style="margin-top:16px; display:flex; flex-wrap:wrap; gap:8px;">
                        <a href="{{ route('programs.show', $program) }}" class="btn btn-secondary">View</a>

                        @if($canManage)
                            <a href="{{ route('programs.edit', $program) }}" class="btn btn-secondary">Edit</a>

                            <form action="{{ route('programs.destroy', $program) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Delete this program?')">Delete</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
