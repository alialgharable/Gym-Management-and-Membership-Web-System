@extends('layouts.app')

@section('title', 'Members')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Members</h1>
            <p class="section-subtitle">Manage gym members and their information</p>
        </div>
        <a href="{{ route('members.create') }}" class="btn btn-primary">+ Add Member</a>
    </div>

    @if ($members->count())
        <div style="overflow-x: auto;">
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
                        <tr>
                            <td>#{{ $member->id }}</td>
                            <td>{{ $member->user->name ?? 'N/A' }}</td>
                            <td>{{ $member->user->email ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $activeSub = $member->subscription()->where('status', 'active')->first();
                                @endphp
                                <span style="color: {{ $activeSub ? '#5fd68f' : '#ff5555' }};">
                                    {{ $activeSub ? '✓ Active' : '✗ Inactive' }}
                                </span>
                            </td>
                            <td>{{ $member->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('members.show', $member) }}" class="btn btn-secondary">View</a>
                                    <a href="{{ route('members.edit', $member) }}" class="btn btn-secondary">Edit</a>
                                    <form action="{{ route('members.destroy', $member) }}" method="POST" style="display:inline;">
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
            No members found. <a href="{{ route('members.create') }}" style="color: #ffd700; font-weight: bold;">Create one now</a>
        </div>
    @endif
@endsection
