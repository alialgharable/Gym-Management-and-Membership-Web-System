@extends('layouts.app')

@section('title', 'Plan Details')

@section('content')

    @php
        $activeSubscriptions = $plan->subscriptions->where('status', 'active')->count();
        $totalSubscriptions = $plan->subscriptions->count();
    @endphp

    <div class="page-header">
        <div>
            <h1 class="section-title">{{ $plan->name }}</h1>
            <p class="section-subtitle">Membership plan details</p>
        </div>

        <div class="actions">
            <a href="{{ route('plans.index') }}" class="btn btn-secondary">Back</a>

            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('plans.edit', $plan) }}" class="btn btn-primary">Edit</a>
                @endif
            @endauth
        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
        <div class="card">
            <h3>Plan Information</h3>
            <p><strong>Price:</strong> ${{ number_format($plan->price, 2) }}</p>
            <p><strong>Duration:</strong> {{ $plan->durationLabel() }}</p>
            <p><strong>Description:</strong> {{ $plan->description ?? 'No description available.' }}</p>
        </div>

        <div class="card">
            <h3>Subscription Overview</h3>
            <p><strong>Active Subscriptions:</strong> {{ $activeSubscriptions }}</p>
            <p><strong>Total Subscriptions:</strong> {{ $totalSubscriptions }}</p>
            <p>
                <strong>Status:</strong>
                <span style="color: {{ $activeSubscriptions > 0 ? '#5fd68f' : '#a9a89d' }}; font-weight:600;">
                    {{ $activeSubscriptions > 0 ? 'Popular Plan' : 'No Active Subscribers' }}
                </span>
            </p>
        </div>
    </div>

    @auth
        @if(!auth()->user()->isAdmin() && !auth()->user()->isTrainer())
            <div style="margin-top:1.5rem;">
                <form action="{{ route('subscriptions.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </form>
            </div>
        @endif
    @endauth

    @auth
        @if(auth()->user()->isAdmin())
            <div style="margin-top:1.5rem;">
                <form action="{{ route('plans.destroy', $plan) }}" method="POST" class="delete-form">
                    @csrf
                    @method('DELETE')

                    <button type="button" class="btn btn-danger btn-delete">
                        Delete Plan
                    </button>
                </form>
            </div>
        @endif
    @endauth

@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {
                const form = this.closest('form');

                window.showModal({
                    type: 'warning',
                    title: 'Delete Plan?',
                    message: 'This plan will be removed permanently.',
                    confirmText: 'Yes, Delete',
                    onConfirm: () => form.submit()
                });
            });
        });
    </script>
@endpush