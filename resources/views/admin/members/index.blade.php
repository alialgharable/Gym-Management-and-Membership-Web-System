@extends('layouts.app')

@section('title', 'Members')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="section-title">Members</h1>
            <p class="section-subtitle">Manage gym members and their information</p>
        </div>
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

            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('members.create') }}" class="btn btn-primary">
                        Create Member
                    </a>
                @endif
            @endauth
        </div>
    @endif

@endsection