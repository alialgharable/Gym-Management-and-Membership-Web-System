@extends('layouts.app')

@section('title', 'Members')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="section-title">Members</h1>
            <p class="section-subtitle">Manage gym members and their information</p>
        </div>
    </div>

    <div style="margin-bottom:1rem; display:flex; gap:8px; align-items:center;">
        <form id="members-filters" method="GET" action="{{ route('members.index') }}" style="display:flex; gap:8px; align-items:center;">
            <input type="text" name="search" placeholder="Search members..." value="{{ request('search') }}" style="padding:8px 10px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); background:transparent; color:inherit;">

            <select name="status" style="padding:8px 10px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); background:transparent; color:inherit;">
                <option value="">All status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <a href="{{ route('members.index') }}" class="btn btn-secondary">Reset</a>
        </form>
    </div>

    @if ($members->count())
        <div class="card">
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subscription Status</th>
                            <th>Member Since</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $member)
                            @php
                                $activeSub = $member->subscription()->where('status', 'active')->first();
                            @endphp
                            <tr>
                                <td>#{{ $member->id }}</td>
                                <td>{{ $member->user->name ?? 'N/A' }}</td>
                                <td>{{ $member->user->email ?? 'N/A' }}</td>
                                <td>
                                    <span style="color: {{ $activeSub ? '#5fd68f' : '#ff5555' }}; font-weight: 600;">
                                        {{ $activeSub ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $member->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('members.show', $member) }}" class="btn btn-secondary">
                                            View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card" style="text-align:center; padding:40px;">
            <h3 style="color:#ffd700;">No Members Found</h3>
            <p style="color:#aaa;">There are no members in the system yet.</p>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        (function(){
            const form = document.getElementById('members-filters');
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