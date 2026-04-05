@extends('layouts.app')

@section('title', 'Apply as Trainer')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Become a Trainer</h1>
            <p class="section-subtitle">Submit your application to join as a trainer</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card" style="max-width: 700px;">
        <form action="{{ route('trainer-applications.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- USER (DISPLAY ONLY) -->
            <div class="field-group">
                <label class="field-label">Applying As</label>
                <input type="text" class="field-input" value="{{ auth()->user()->name }} ({{ auth()->user()->email }})"
                    disabled>
            </div>

            <!-- CV FILE -->
            <div class="field-group">
                <label class="field-label">Upload CV <span style="color:#ff5555;">*</span></label>
                <input type="file" name="cv_file" class="field-input" required>
                @error('cv_file')
                    <span style="color:#ff5555;">{{ $message }}</span>
                @enderror
            </div>

            <!-- EXPERIENCE -->
            <div class="field-group">
                <label class="field-label">Experience <span style="color:#ff5555;">*</span></label>
                <textarea name="experience" class="field-input" rows="4" placeholder="Describe your experience..."
                    required>{{ old('experience') }}</textarea>
                @error('experience')
                    <span style="color:#ff5555;">{{ $message }}</span>
                @enderror
            </div>

            <!-- CERTIFICATIONS -->
            <div class="field-group">
                <label class="field-label">Certifications (optional)</label>
                <textarea name="certifications" class="field-input" rows="3"
                    placeholder="List your certifications...">{{ old('certifications') }}</textarea>
                @error('certifications')
                    <span style="color:#ff5555;">{{ $message }}</span>
                @enderror
            </div>

            <!-- SUBMIT -->
            <div style="display:flex; gap:1rem; margin-top:2rem;">
                <button type="submit" class="btn btn-primary">
                    Submit Application
                </button>
                <a href="{{ route('home') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>

        </form>
    </div>
@endsection