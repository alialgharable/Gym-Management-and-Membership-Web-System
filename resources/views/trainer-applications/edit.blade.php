@extends('layouts.app')

@section('title', 'Edit Application')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Edit Application</h1>
            <p class="section-subtitle">Update application status</p>
        </div>
        <a href="{{ route('trainer-applications.show', $application) }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('trainer-applications.update', $application) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field-group">
                <label class="field-label">Applicant</label>
                <input type="text" value="{{ $application->user->name ?? '' }}" class="field-input" disabled>
            </div>

            <div class="field-group">
                <label class="field-label">Status <span style="color: #ff5555;">*</span></label>
                <select name="status" class="field-select" required>
                    <option value="pending" @selected($application->status == 'pending')>Pending</option>
                    <option value="approved" @selected($application->status == 'approved')>Approved</option>
                    <option value="rejected" @selected($application->status == 'rejected')>Rejected</option>
                </select>
                @error('status')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('trainer-applications.show', $application) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
