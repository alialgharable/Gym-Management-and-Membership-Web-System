@extends('layouts.app')

@section('title', 'Create Member')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Create New Member</h1>
            <p class="section-subtitle">Add a new member to the system</p>
        </div>
        <a href="{{ route('members.index') }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('members.store') }}" method="POST">
            @csrf

            <div class="field-group">
                <label class="field-label">User <span style="color: #ff5555;">*</span></label>
                <select name="user_id" class="field-select" required>
                    <option value="">Select a user...</option>
                    @foreach (\App\Models\User::all() as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Create Member</button>
                <a href="{{ route('members.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
