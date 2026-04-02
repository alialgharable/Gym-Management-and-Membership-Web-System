@extends('layouts.app')

@section('title', 'Admins')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Administrators</h1>
            <p class="section-subtitle">Manage system administrators</p>
        </div>
        <a href="{{ route('admins.create') }}" class="btn btn-primary">+ Add Admin</a>
    </div>

    @if ($admins->count())
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($admins as $admin)
                        <tr>
                            <td>#{{ $admin->id }}</td>
                            <td>{{ $admin->user->name ?? 'N/A' }}</td>
                            <td>{{ $admin->user->email ?? 'N/A' }}</td>
                            <td>{{ $admin->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admins.show', $admin) }}" class="btn btn-secondary">View</a>
                                    <a href="{{ route('admins.edit', $admin) }}" class="btn btn-secondary">Edit</a>
                                    <form action="{{ route('admins.destroy', $admin) }}" method="POST" style="display:inline;">
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
            No administrators found. <a href="{{ route('admins.create') }}" style="color: #ffd700; font-weight: bold;">Add one now</a>
        </div>
    @endif
@endsection
