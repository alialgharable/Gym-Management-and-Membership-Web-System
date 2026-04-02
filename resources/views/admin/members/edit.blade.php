@extends('layouts.app')

@section('title', 'Edit Member')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Edit Member</h1>
            <p class="section-subtitle">Update member information</p>
        </div>
        <a href="{{ route('members.show', $member) }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('members.update', $member) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field-group">
                <label class="field-label">Member Name</label>
                <input type="text" value="{{ $member->user->name ?? '' }}" class="field-input" disabled>
                <small style="color: #a9a89d;">User information cannot be edited here</small>
            </div>

            <div class="field-group">
                <label class="field-label">Member Email</label>
                <input type="email" value="{{ $member->user->email ?? '' }}" class="field-input" disabled>
            </div>

            <div class="field-group">
                <label class="field-label">Member Since</label>
                <input type="text" value="{{ $member->created_at->format('M d, Y') }}" class="field-input" disabled>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('members.show', $member) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
