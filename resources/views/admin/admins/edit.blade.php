@extends('layouts.app')

@section('title', 'Edit Admin')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Edit Administrator</h1>
            <p class="section-subtitle">Update admin information</p>
        </div>
        <a href="{{ route('admins.show', $admin) }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('admins.update', $admin) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field-group">
                <label class="field-label">Admin Name</label>
                <input type="text" value="{{ $admin->user->name ?? '' }}" class="field-input" disabled>
                <small style="color: #a9a89d;">User information cannot be edited here</small>
            </div>

            <div class="field-group">
                <label class="field-label">Admin Email</label>
                <input type="email" value="{{ $admin->user->email ?? '' }}" class="field-input" disabled>
            </div>

            <div class="field-group">
                <label class="field-label">Admin Since</label>
                <input type="text" value="{{ $admin->created_at->format('M d, Y') }}" class="field-input" disabled>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('admins.show', $admin) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
