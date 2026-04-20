@extends('layouts.app')

@section('title', 'Edit Class')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="section-title">Edit Class</h1>
            <p class="section-subtitle">Update class information</p>
        </div>
        <a href="{{ route('classes.show', $gymClass->id) }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card" style="max-width:700px; margin:0 auto;">
        <form id="class-edit-form">
            @csrf
            @method('PUT')

            <div class="field-group">
                <label class="field-label">Class Name</label>
                <input type="text" name="name" class="field-input" required>
            </div>

            <div class="field-group">
                <label class="field-label">Trainer</label>
                <select name="trainer_id" class="field-select" {{ auth()->check() && auth()->user()->isTrainer() ? 'disabled' : 'required' }}>
                    @foreach ($trainers as $trainer)
                        <option value="{{ $trainer->id }}">
                            {{ $trainer->user->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field-group">
                <label class="field-label">Schedule</label>
                <input type="datetime-local" name="schedule" class="field-input">
            </div>

            <div class="field-group">
                <label class="field-label">Category</label>
                <select name="category" class="field-select" required>
                    @if(auth()->check() && auth()->user()->isTrainer())
                        @php
                            $trainerSpecialty = auth()->user()->trainer->specialty ?? null;
                            $trainerSpecialtyLabel = auth()->user()->trainer ? auth()->user()->trainer->specialtyLabel() : ($trainerSpecialty ? (\App\Models\Trainer::SPECIALTIES[$trainerSpecialty] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $trainerSpecialty))) : 'My Specialty');
                        @endphp
                        <option value="{{ $trainerSpecialty }}" selected>
                            {{ $trainerSpecialtyLabel }}
                        </option>
                    @else
                        @foreach (\App\Models\Trainer::SPECIALTIES as $value => $label)
                            <option value="{{ $value }}">
                                {{ $label }}
                            </option>
                        @endforeach
                    @endif
                </select>

                <small style="color:#888; display:block; margin-top:4px;">
                    @if(auth()->check() && auth()->user()->isTrainer())
                        You can only assign classes to your specialty.
                    @else
                        Room is assigned automatically based on category.
                    @endif
                </small>
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
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('classes.show', $gymClass->id) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
    <script>
    const classId = @json($gymClass->id);
    const isTrainer = @json(auth()->check() && auth()->user()->isTrainer());

    async function loadClassForEdit() {
        const form = document.getElementById('class-edit-form');

        try {
            const response = await fetch(`/api/classes/${classId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to load class');
            }

            const result = await response.json();
            const gymClass = result.data || {};

            form.querySelector('[name="name"]').value = gymClass.name ?? '';
            form.querySelector('[name="schedule"]').value = gymClass.schedule
                ? new Date(new Date(gymClass.schedule).getTime() - new Date(gymClass.schedule).getTimezoneOffset() * 60000)
                    .toISOString()
                    .slice(0, 16)
                : '';
            form.querySelector('[name="category"]').value = gymClass.category ?? '';
            form.querySelector('[name="capacity"]').value = gymClass.capacity ?? '';
            form.querySelector('[name="description"]').value = gymClass.description ?? '';

            const trainerSelect = form.querySelector('[name="trainer_id"]');
            if (trainerSelect && !isTrainer) {
                trainerSelect.value = gymClass.trainer_id ?? '';
            }
        } catch (error) {
            if (window.showModal) {
                window.showModal({
                    type: 'error',
                    title: 'Error',
                    message: 'Failed to load class details.',
                    confirmText: 'OK'
                });
            } else {
                alert('Failed to load class details.');
            }
        }
    }

    const form = document.getElementById('class-edit-form');

    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            formData.append('_method', 'PUT');

            try {
                const response = await fetch(`/api/classes/${classId}`, {
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

                if (window.showModal) {
                    window.showModal({
                        type: 'success',
                        title: 'Success',
                        message: result.message || 'Class updated successfully!',
                        confirmText: 'OK',
                        onConfirm: () => {
                            window.location.href = `/classes/${classId}`;
                        }
                    });
                } else {
                    alert(result.message || 'Class updated successfully!');
                    window.location.href = `/classes/${classId}`;
                }
            } catch (error) {
                let message = 'Failed to update class.';

                if (error.message) {
                    message = error.message;
                } else if (error.errors) {
                    message = Object.values(error.errors).flat().join('\n');
                }

                if (window.showModal) {
                    window.showModal({
                        type: 'error',
                        title: 'Error',
                        message: message,
                        confirmText: 'OK'
                    });
                } else {
                    alert(message);
                }
            }
        });
    }

    loadClassForEdit();
</script>
@endpush