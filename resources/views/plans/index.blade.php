@extends('layouts.app')

@section('title', 'Membership Plans')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="section-title">Membership Plans</h1>
            <p class="section-subtitle">Choose the perfect plan for your fitness journey</p>
        </div>

        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ route('plans.create') }}" class="btn btn-primary">
                    + Create Plan
                </a>
            @endif
        @endauth
    </div>

    @if (!$plans->count())
        <div class="card" style="text-align:center; padding:40px;">
            <h3 style="color:#ffd700;">No Plans Available</h3>
            <p style="color:#aaa;">Start by creating your first membership plan.</p>

            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('plans.create') }}" class="btn btn-primary">
                        + Create Plan
                    </a>
                @endif
            @endauth
        </div>
    @else

        <div class="card-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">

            @foreach ($plans as $plan)
                <div class="card" style="display:flex; flex-direction:column; justify-content:space-between;">

                    <div>
                        <h3 style="color:#f7d34a; margin-bottom:10px;">
                            {{ $plan->name }}
                        </h3>

                        <div style="margin-bottom:10px;">
                            <span style="font-size:2rem; font-weight:700; color:#5fd68f;">
                                ${{ number_format($plan->price, 2) }}
                            </span>
                            <span style="color:#aaa; font-size:0.9rem;">
                                / {{ $plan->durationLabel() }}
                            </span>
                        </div>

                        <p style="color:#d7d2ad; line-height:1.6; min-height:60px;">
                            {{ $plan->description ?? 'No description available for this plan.' }}
                        </p>
                    </div>

                    <div class="actions" style="margin-top:20px; display:flex; flex-wrap:wrap; gap:8px;">

                        <a href="{{ route('plans.show', $plan) }}" class="btn btn-secondary">
                            View
                        </a>

                        @auth
                            @if(!auth()->user()->isAdmin() && !auth()->user()->isTrainer())
                                <form action="{{ route('subscriptions.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">

                                    <button class="btn btn-primary">
                                        Subscribe
                                    </button>
                                </form>
                            @endif
                        @endauth

                        @auth
                            @if(auth()->user()->isAdmin())

                                <a href="{{ route('plans.edit', $plan) }}" class="btn btn-secondary">
                                    Edit
                                </a>

                                <form action="{{ route('plans.destroy', $plan) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')

                                    <button type="button" class="btn btn-danger btn-delete">
                                        Delete
                                    </button>
                                </form>

                            @endif
                        @endauth

                    </div>
                </div>
            @endforeach

        </div>
    @endif

@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {
                const form = this.closest('form');

                window.showModal({
                    type: 'warning',
                    title: 'Delete Plan?',
                    message: 'This action cannot be undone.',
                    confirmText: 'Yes, Delete',
                    onConfirm: () => form.submit()
                });
            });
        });
    </script>
@endpush