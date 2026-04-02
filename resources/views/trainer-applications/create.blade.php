@extends('layouts.app')

@section('title', 'Create Application')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Create New Application</h1>
            <p class="section-subtitle">Submit a new trainer application</p>
        </div>
        <a href="{{ route('trainer-applications.index') }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('trainer-applications.store') }}" method="POST">
            @csrf

            <div class="field-group">
                <label class="field-label">User <span style="color: #ff5555;">*</span></label>
                <select name="user_id" class="field-select" required>
                    <option value="">Select a user...</option>
                    @foreach ($users as $user)
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
                <button type="submit" class="btn btn-primary">Create Application</button>
                <a href="{{ route('trainer-applications.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
