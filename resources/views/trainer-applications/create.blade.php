@extends('layouts.app')

@section('title', 'Apply as Trainer')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="section-title">Become a Trainer</h1>
            <p class="section-subtitle">Submit your application to join as a trainer</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card" style="max-width:700px; margin:0 auto;">
        <form action="{{ route('trainer-applications.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="field-group">
                <label class="field-label">Applying As</label>
                <input
                    type="text"
                    class="field-input"
                    value="{{ auth()->user()->name }} ({{ auth()->user()->email }})"
                    disabled
                >
            </div>

            <div class="field-group">
                <label class="field-label">Upload CV</label>
                <input type="file" name="cv_file" class="field-input" required>
                @error('cv_file')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Experience</label>
                <textarea
                    name="experience"
                    class="field-input"
                    rows="5"
                    style="resize:vertical;"
                    placeholder="Describe your training experience, background, and skills..."
                    required
                >{{ old('experience') }}</textarea>
                @error('experience')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Certifications</label>
                <textarea
                    name="certifications"
                    class="field-input"
                    rows="4"
                    style="resize:vertical;"
                    placeholder="List any certifications or qualifications..."
                >{{ old('certifications') }}</textarea>
                @error('certifications')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:2rem;">
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