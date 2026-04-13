@extends('layouts.app')

@section('title', 'Application Details')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">{{ $application->user->name ?? 'Applicant' }}'s Application</h1>
            <p class="section-subtitle">Application #{{ $application->id }}</p>
        </div>
        <div class="actions">
            <a href="{{ route('trainer-applications.index') }}" class="btn btn-secondary">← Back</a>
            <a href="{{ route('trainer-applications.edit', $application) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: 1fr 1fr;">
        <div class="card">
            <h3>Applicant Information</h3>
            <p><strong>Name:</strong> {{ $application->user->name ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $application->user->email ?? 'N/A' }}</p>
            <p><strong>Applied On:</strong> {{ $application->created_at->format('M d, Y') }}</p>
        </div>

        <div class="card">
            <h3>Application Status</h3>

            <p>
                <strong>Status:</strong>
                <span
                    style="color: {{ $application->status === 'approved' ? '#5fd68f' : ($application->status === 'rejected' ? '#ff5555' : '#ffd700') }};">
                    {{ ucfirst($application->status) }}
                </span>
            </p>

            <p><strong>Experience:</strong> {{ $application->experience ?? 'N/A' }}</p>

            <p style="margin-top: 1rem;">
                <strong>CV:</strong>
                @if($application->cv_file)
                    <a href="{{ asset('storage/' . $application->cv_file) }}" target="_blank" class="btn btn-secondary"
                        style="margin-left: 10px;">
                        View CV
                    </a>
                @else
                    <span style="color: #aaa;">No CV uploaded</span>
                @endif
            </p>
        </div>

        <div style="margin-top: 1.5rem;">
            <form action="{{ route('trainer-applications.destroy', $application) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"
                    onclick="return confirm('This will permanently delete this application. Are you sure?')">Delete
                    Application</button>
            </form>
        </div>
@endsection