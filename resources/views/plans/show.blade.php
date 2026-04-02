@extends('layouts.app')

@section('title', 'Plan Details')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">{{ $plan->name }}</h1>
            <p class="section-subtitle">Membership Plan Details</p>
        </div>
        <div class="actions">
            <a href="{{ route('plans.index') }}" class="btn btn-secondary">← Back</a>
            <a href="{{ route('plans.edit', $plan) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: 1fr 1fr;">
        <div class="card">
            <h3>Plan Information</h3>
            <p><strong>Price:</strong> ${{ number_format($plan->price, 2) }}</p>
            <p><strong>Duration:</strong> {{ $plan->duration_months }} months</p>
            <p><strong>Description:</strong> {{ $plan->description ?? 'N/A' }}</p>
        </div>

        <div class="card">
            <h3>Subscriptions</h3>
            <p><strong>Active:</strong> {{ $plan->subscriptions->where('status', 'active')->count() }}</p>
            <p><strong>Total:</strong> {{ $plan->subscriptions->count() }}</p>
        </div>
    </div>

    <div style="margin-top: 1.5rem;">
        <form action="{{ route('plans.destroy', $plan) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('This will permanently delete this plan. Are you sure?')">Delete Plan</button>
        </form>
    </div>
@endsection
