@extends('layouts.app')

@section('title', 'Subscriptions')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Subscriptions</h1>
            <p class="section-subtitle">Manage member subscriptions</p>
        </div>
        <a href="{{ route('subscriptions.create') }}" class="btn btn-primary">+ New Subscription</a>
    </div>

    @if ($subscriptions->count())
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Member</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Valid Until</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subscriptions as $subscription)
                        <tr>
                            <td>#{{ $subscription->id }}</td>
                            <td>{{ $subscription->member->user->name ?? 'N/A' }}</td>
                            <td>{{ $subscription->plan->name ?? 'N/A' }}</td>
                            <td>
                                <span style="color: {{ $subscription->status === 'active' ? '#5fd68f' : ($subscription->status === 'expired' ? '#ff5555' : '#ffd700') }};">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </td>
                            <td>{{ $subscription->end_date->format('M d, Y') }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('subscriptions.show', $subscription) }}" class="btn btn-secondary">View</a>
                                    <a href="{{ route('subscriptions.edit', $subscription) }}" class="btn btn-secondary">Edit</a>
                                    <form action="{{ route('subscriptions.destroy', $subscription) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-warning">
            No subscriptions found. <a href="{{ route('subscriptions.create') }}" style="color: #ffd700; font-weight: bold;">Create one now</a>
        </div>
    @endif
@endsection
