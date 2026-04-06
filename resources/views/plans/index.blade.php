@extends('layouts.app')

@section('title', 'Membership Plans')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Membership Plans</h1>
            <p class="section-subtitle">Manage gym membership plans</p>
        </div>
        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ route('plans.create') }}" class="btn btn-primary">+ New Plan</a>
            @endif
        @endauth

    </div>

    @if ($plans->count())
        <div class="card-grid">
            @foreach ($plans as $plan)
                <div class="card">
                    <h3 style="color: #f7d34a;">{{ $plan->name }}</h3>
                    <p style="font-size: 2rem; font-weight: 700; color: #5fd68f; margin: 0;">
                        ${{ number_format($plan->price, 2) }}
                    </p>
                    <p style="color: #a9a89d; margin: 10px 0;">{{ $plan->duration_months }} months</p>
                    <p style="color: #d7d2ad; line-height: 1.6;">{{ $plan->description ?? 'No description' }}</p>
                    <p style="font-size: 0.9rem; color: #a9a89d;">Active Subscriptions: {{ $plan->subscriptions->count() }}</p>
                    <div class="actions" style="margin-top: 1rem;">
                        <a href="{{ route('plans.show', $plan) }}" class="btn btn-secondary">View</a>
                         @auth
                            @if(auth()->user()->isAdmin())
                        <a href="{{ route('plans.edit', $plan) }}" class="btn btn-secondary">Edit</a>
                        <form action="{{ route('plans.destroy', $plan) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                           @endif
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning">
            No plans found. <a href="{{ route('plans.create') }}" style="color: #ffd700; font-weight: bold;">Create one now</a>
        </div>
    @endif
@endsection