@extends('layouts.app')

@section('title', 'Admin Details')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">{{ $admin->user->name ?? 'Administrator' }}</h1>
            <p class="section-subtitle">Admin Profile</p>
        </div>
        <div class="actions">
            <a href="{{ route('admins.index') }}" class="btn btn-secondary">← Back</a>
            <a href="{{ route('admins.edit', $admin) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: 1fr 1fr;">
        <div class="card">
            <h3>Administrator Information</h3>
            <p><strong>Name:</strong> {{ $admin->user->name ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $admin->user->email ?? 'N/A' }}</p>
            <p><strong>Admin Since:</strong> {{ $admin->created_at->format('M d, Y') }}</p>
        </div>

        <div class="card">
            <h3>Permissions</h3>
            <p style="color: #5fd68f;">✓ Full System Access</p>
            <p style="color: #5fd68f;">✓ User Management</p>
            <p style="color: #5fd68f;">✓ Dashboard Access</p>
        </div>
    </div>

    <div style="margin-top: 1.5rem;">
        <form action="{{ route('admins.destroy', $admin) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('This will permanently delete this admin. Are you sure?')">Delete Admin</button>
        </form>
    </div>
@endsection
