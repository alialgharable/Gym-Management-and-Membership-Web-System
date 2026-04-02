@extends('layouts.app')

@section('title', 'Member Dashboard')

@section('content')
    @if($member)
        <div class="page-header">
            <div>
                <h1 class="section-title">Welcome, {{ $member->user->name ?? 'Member' }}</h1>
                <p class="section-subtitle">View your active membership details, account information, and your joined date.</p>
            </div>
        </div>

        <div class="grid-stack">
            <section class="card">
                <h2>Membership Plan</h2>
                <p>{{ $member->subscription()->where('status', 'active')->first()?->plan?->name ?? 'No active subscription' }}</p>
            </section>

            <section class="card">
                <h2>Contact</h2>
                <p>{{ $member->user->email ?? 'No email available' }}</p>
            </section>

            <section class="card">
                <h2>Member Since</h2>
                <p>{{ $member->created_at ? $member->created_at->format('F j, Y') : 'N/A' }}</p>
            </section>
        </div>
    @else
        <div class="alert">
            <strong>No Member Record</strong>
            <p>You are not subscribed to any membership plan yet. Please contact support or register a membership.</p>
        </div>
    @endif
@endsection