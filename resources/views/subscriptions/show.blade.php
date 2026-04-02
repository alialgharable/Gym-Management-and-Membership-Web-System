@extends('layouts.app')

@section('title', 'Subscription Details')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">{{ $subscription->member->user->name ?? 'Member' }} - {{ $subscription->plan->name ?? 'Plan' }}</h1>
            <p class="section-subtitle">Subscription #{{ $subscription->id }}</p>
        </div>
        <div class="actions">
            <a href="{{ route('subscriptions.index') }}" class="btn btn-secondary">← Back</a>
            <a href="{{ route('subscriptions.edit', $subscription) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: 1fr 1fr;">
        <div class="card">
            <h3>Member Information</h3>
            <p><strong>Name:</strong> {{ $subscription->member->user->name ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $subscription->member->user->email ?? 'N/A' }}</p>
        </div>

        <div class="card">
            <h3>Plan Information</h3>
            <p><strong>Plan:</strong> {{ $subscription->plan->name ?? 'N/A' }}</p>
            <p><strong>Price:</strong> ${{ number_format($subscription->plan->price ?? 0, 2) }}</p>
            <p><strong>Duration:</strong> {{ $subscription->plan->duration_months ?? 'N/A' }} months</p>
        </div>
    </div>

    <div class="card" style="margin-top: 1.5rem;">
        <h3>Subscription Status</h3>
        <p><strong>Status:</strong> <span style="color: {{ $subscription->status === 'active' ? '#5fd68f' : ($subscription->status === 'expired' ? '#ff5555' : '#ffd700') }};">{{ ucfirst($subscription->status) }}</span></p>
        <p><strong>Start Date:</strong> {{ $subscription->start_date->format('M d, Y') }}</p>
        <p><strong>End Date:</strong> {{ $subscription->end_date->format('M d, Y') }}</p>
    </div>

    <div style="margin-top: 1.5rem;">
        <form action="{{ route('subscriptions.destroy', $subscription) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('This will permanently delete this subscription. Are you sure?')">Delete Subscription</button>
        </form>
    </div>
@endsection
