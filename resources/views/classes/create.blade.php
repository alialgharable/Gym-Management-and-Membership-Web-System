@extends('layouts.app')

@section('title', 'Create Class')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="section-title">Create New Class</h1>
            <p class="section-subtitle">Add a new gym class</p>
        </div>
        <a href="{{ auth()->user()->isTrainer() ? route('trainer.dashboard') : route('classes.index') }}"
            class="btn btn-secondary">
            Back
        </a>
    </div>

    <div class="card" style="max-width:700px; margin:0 auto;">
        <form id="class-create-form">
            @csrf

            <div class="field-group">
                <label class="field-label">Class Name</label>
                <input type="text" name="name" class="field-input" required>
            </div>

            @if ($isTrainer)
                <div class="field-group">
                    <label class="field-label">Trainer</label>
                    <div class="field-input"
                        style="padding:0.75rem; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08);">
                        {{ auth()->user()->trainer->user->name ?? 'Your Profile' }}
                    </div>
                </div>
            @else
                <div class="field-group">
                    <label class="field-label">Trainer</label>
                    <select name="trainer_id" class="field-select" required>
                        <option value="">Select a trainer...</option>
                        @foreach ($trainers as $trainer)
                            <option value="{{ $trainer->id }}">
                                {{ $trainer->user->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="field-group">
                <label class="field-label">Schedule</label>
                <input type="datetime-local" name="schedule" class="field-input">
            </div>

            <div class="field-group">
                <label class="field-label">Category</label>
                <select name="category" class="field-select" required>
                    @if ($isTrainer)
                        @php
                            $trainerSpecialty = auth()->user()->trainer->specialty ?? null;
                            $trainerSpecialtyLabel = auth()->user()->trainer ? auth()->user()->trainer->specialtyLabel() : ($trainerSpecialty ? (\App\Models\Trainer::SPECIALTIES[$trainerSpecialty] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $trainerSpecialty))) : 'My Specialty');
                        @endphp
                        <option value="{{ $trainerSpecialty }}" selected>
                            {{ $trainerSpecialtyLabel }}
                        </option>
                    @else
                        <option value="">Select a category...</option>
                        @foreach (\App\Models\Trainer::SPECIALTIES as $value => $label)
                            <option value="{{ $value }}">
                                {{ $label }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="field-group">
                <label class="field-label" style="display:flex; align-items:center; gap:0.6rem;">
                    <input type="checkbox" name="create_full_month" value="1">
                    Create full month schedule
                </label>
            </div>

            <div class="field-group">
                <label class="field-label">Capacity</label>
                <input type="number" name="capacity" class="field-input" min="1" max="30">
            </div>

            <div class="field-group">
                <label class="field-label">Description</label>
                <textarea name="description" class="field-input" rows="4" style="resize:vertical;"></textarea>
            </div>

            <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:2rem;">
                <button type="submit" class="btn btn-primary">Create Class</button>
                <a href="{{ route('classes.index') }}" class="btn btn-secondary">Cancel</a>
            </div>

        </form>
    </div>

@endsection

@push('scripts')
    <script>
        const form = document.getElementById('class-create-form');

        if (form) {
            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                const formData = new FormData(form);

                try {
                    const response = await fetch('/api/classes', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        throw result;
                    }

                    window.showModal({
                        type: 'success',
                        title: 'Success',
                        message: result.message || 'Class created successfully!',
                        confirmText: 'OK',
                        onConfirm: () => {
                            window.location.href = '/classes';
                        }
                    });
                } catch (error) {
                    let message = 'Failed to create class.';

                    if (error.message) {
                        message = error.message;
                    } else if (error.errors) {
                        message = Object.values(error.errors).flat().join('\n');
                    }

                    window.showModal({
                        type: 'error',
                        title: 'Error',
                        message: message,
                        confirmText: 'OK'
                    });
                }
            });
        }
    </script>
@endpush