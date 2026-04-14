@extends('layouts.app')

@section('title', 'Edit Application')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="section-title">Edit Application</h1>
            <p class="section-subtitle">Update your trainer application details</p>
        </div>
        <a href="{{ route('trainer-applications.show', $application) }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card" style="max-width:800px; margin:0 auto;">
        <form action="{{ route('trainer-applications.update', $application) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="field-group">
                <label class="field-label">Applicant</label>
                <input type="text" value="{{ $application->user->name ?? '' }}" class="field-input" disabled>
            </div>

            <div class="field-group">
                <label class="field-label">Current Status</label>
                <input type="text" value="{{ ucfirst($application->status) }}" class="field-input" disabled>
            </div>

            <div class="field-group">
                <label class="field-label">Experience</label>
                <textarea name="experience" class="field-input" rows="6" style="resize:vertical;"
                    required>{{ old('experience', $application->experience) }}</textarea>
                @error('experience')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Certifications</label>
                <textarea name="certifications" class="field-input" rows="6"
                    style="resize:vertical;">{{ old('certifications', $application->certifications) }}</textarea>
                @error('certifications')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Replace CV</label>
                <input type="file" name="cv_file" class="field-input" accept=".pdf,.doc,.docx">

                @if($application->cv_file)
                    <p style="margin-top:0.5rem; color:#aaa;">
                        Current file:
                        <a href="{{ asset('storage/' . $application->cv_file) }}" target="_blank"
                            style="color:#f7d34a; font-weight:600;">
                            View current CV
                        </a>
                    </p>
                @endif

                @error('cv_file')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:2rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('trainer-applications.show', $application) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

@endsection