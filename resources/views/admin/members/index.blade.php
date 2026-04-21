@extends('layouts.app')

@section('title', 'Members')

@section('content')

    <style>
        .members-filters-wrap {
            margin-bottom: 1rem;
        }

        .members-filters {
            display: grid;
            gap: 0.75rem;
            grid-template-columns: minmax(220px, 1.6fr) minmax(170px, 0.8fr) auto;
            align-items: center;
            padding: 0.85rem;
            border-radius: 14px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.035), rgba(255, 255, 255, 0.02));
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .members-filter-input,
        .members-filter-select {
            width: 100%;
            min-height: 42px;
            padding: 0.62rem 0.8rem;
            font: inherit;
            font-size: 0.95rem;
            font-weight: 500;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.11);
            background: rgba(10, 16, 26, 0.85);
            color: #edf2f7;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .members-filter-input::placeholder {
            color: #9da8b8;
        }

        .members-filter-input:focus,
        .members-filter-select:focus {
            outline: none;
            border-color: rgba(247, 211, 74, 0.75);
            box-shadow: 0 0 0 3px rgba(247, 211, 74, 0.16);
            background: rgba(12, 20, 31, 0.94);
        }

        .members-filter-select option {
            color: #e9edf3;
            background: #111922;
        }

        .member-status-active {
            color: #5fd68f;
            font-weight: 600;
        }

        .member-status-inactive {
            color: #ff7a7a;
            font-weight: 600;
        }

        .member-actions {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: nowrap;
            white-space: nowrap;
        }

        .member-delete-form {
            margin: 0;
            display: inline;
        }

        .member-delete-form .btn {
            font: inherit;
            font-size: 0.9rem;
            font-weight: 600;
        }

        @media (max-width: 820px) {
            .members-filters {
                grid-template-columns: 1fr;
            }

            .members-filters .btn {
                width: 100%;
            }
        }
    </style>

    <div class="page-header">
        <div>
            <h1 class="section-title">Members</h1>
            <p class="section-subtitle">Manage gym members and their information</p>
        </div>
    </div>

    <div class="members-filters-wrap">
        <form id="members-filters" method="GET" action="{{ route('members.index') }}" class="members-filters">
            <input type="text" name="search" placeholder="Search members..." value="{{ request('search') }}" class="members-filter-input">

            <select name="status" class="members-filter-select">
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
                                $isInactive = !$activeSub;
                            @endphp
                            <tr>
                                <td>#{{ $member->id }}</td>
                                <td>{{ $member->user->name ?? 'N/A' }}</td>
                                <td>{{ $member->user->email ?? 'N/A' }}</td>
                                <td>
                                    <span class="{{ $isInactive ? 'member-status-inactive' : 'member-status-active' }}">
                                        {{ $activeSub ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $member->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="member-actions">
                                        <a href="{{ route('members.show', $member) }}" class="btn btn-secondary">
                                            View
                                        </a>

                                        @if ($isInactive)
                                            <form action="{{ route('members.destroy', $member) }}" method="POST" class="member-delete-form"
                                                onsubmit="return confirm('Delete this inactive member? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                            </form>
                                        @endif
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