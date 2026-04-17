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

    <div style="margin-bottom:1rem; display:flex; gap:8px; align-items:center;">
        <form id="subscriptions-filters" method="GET" action="{{ route('subscriptions.index') }}" style="display:flex; gap:8px; align-items:center;">
            <input type="text" name="search" placeholder="Search subscriptions..." value="{{ request('search') }}" style="padding:8px 10px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); background:transparent; color:inherit;">

            <select name="plan_id" style="padding:8px 10px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); background:transparent; color:inherit;">
                <option value="">All plans</option>
                @foreach(\App\Models\MembershipPlan::all() as $p)
                    <option value="{{ $p->id }}" {{ request('plan_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>

            <select name="status" style="padding:8px 10px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); background:transparent; color:inherit;">
                <option value="">Any status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <a href="{{ route('subscriptions.index') }}" class="btn btn-secondary">Reset</a>
        </form>
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

@push('scripts')
    <script>
        (function(){
            const form = document.getElementById('subscriptions-filters');
            if (!form) return;

            let timer;
            const submit = () => form.submit();

            const search = form.querySelector('input[name="search"]');
            if (search) {
                search.addEventListener('input', function () {
                    clearTimeout(timer);
                    timer = setTimeout(submit, 500);
                });
            }

            form.querySelectorAll('select').forEach(s => s.addEventListener('change', submit));
        })();
    </script>
@endpush
