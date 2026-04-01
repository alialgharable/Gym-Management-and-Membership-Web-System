@extends('layouts.app')

@section('title', 'Member Dashboard')

@section('content')
<div class="container">

    @if($member)
        <h1 class="mb-4">Welcome, {{ $member->user->name ?? 'User' }}</h1>

        <div class="card mb-3">
            <div class="card-body">
                <h5>Membership Plan</h5>
                <p>{{ $member->subscription()->where('status', 'active')->first()?->plan?->name ?? 'No active subscription' }}</p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5>Email</h5>
                <p>{{ $member->user->email ?? 'No email available' }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5>Member Since</h5>
                <p>{{ $member->created_at ? $member->created_at->format('Y-m-d') : 'N/A' }}</p>
            </div>
        </div>

    @else
        <div class="alert alert-warning">
            <h4>No Member Found</h4>
            <p>You are not subscribed to any membership plan yet.</p>
        </div>
    @endif

</div>
@endsection