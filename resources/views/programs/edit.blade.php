@extends('layouts.app')

@section('title', 'Edit Program')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Edit Program</h1>
            <p class="section-subtitle">Update the assigned member and training details.</p>
        </div>
        <a href="{{ route('programs.show', $program) }}" class="btn btn-secondary">Back</a>
    </div>

    <form action="{{ route('programs.update', $program) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid-stack" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
            <div class="field-group">
                <label class="field-label" for="trainer_id">Coach</label>
                <select id="trainer_id" name="trainer_id" class="field-select" {{ auth()->user()->isTrainer() ? 'disabled' : '' }} required>
                    <option value="">Select coach</option>
                    @foreach($trainers as $trainer)
                        <option value="{{ $trainer->id }}"
                            {{ (string) old('trainer_id', $selectedTrainerId) === (string) $trainer->id ? 'selected' : '' }}>
                            {{ $trainer->user->name ?? 'Coach #' . $trainer->id }} - {{ $trainer->specialtyLabel() }}
                        </option>
                    @endforeach
                </select>
                @if(auth()->user()->isTrainer())
                    <input type="hidden" name="trainer_id" value="{{ $selectedTrainerId }}">
                @endif
            </div>

            <div class="field-group">
                <label class="field-label" for="member_id">Assigned Member</label>
                <select id="member_id" name="member_id" class="field-select" required>
                    <option value="">Select member</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}" data-trainer-id="{{ $member->trainer_id }}"
                            {{ (string) old('member_id', $program->member_id) === (string) $member->id ? 'selected' : '' }}>
                            {{ $member->user->name ?? 'Member #' . $member->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field-group">
                <label class="field-label" for="title">Program Title</label>
                <input id="title" name="title" class="field-input" type="text"
                    value="{{ old('title', $program->title) }}" required>
            </div>

            <div class="field-group">
                <label class="field-label" for="duration_weeks">Duration (weeks)</label>
                <input id="duration_weeks" name="duration_weeks" class="field-input" type="number" min="1" max="52"
                    value="{{ old('duration_weeks', $program->duration_weeks) }}" required>
            </div>
        </div>

        <div class="field-group">
            <label class="field-label" for="goal">Goal</label>
            <textarea id="goal" name="goal" class="field-input" rows="3">{{ old('goal', $program->goal) }}</textarea>
        </div>

        <div class="field-group">
            <label class="field-label" for="notes">Program Notes</label>
            <textarea id="notes" name="notes" class="field-input" rows="8">{{ old('notes', $program->notes) }}</textarea>
        </div>

        <div class="actions" style="display:flex; gap:8px;">
            <button class="btn btn-primary" type="submit">Update Program</button>
            <a href="{{ route('programs.show', $program) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

    <script>
        (function () {
            const trainerSelect = document.getElementById('trainer_id');
            const memberSelect = document.getElementById('member_id');

            function syncMembersByCoach() {
                const selectedTrainerId = trainerSelect ? trainerSelect.value : '{{ $selectedTrainerId }}';
                const options = Array.from(memberSelect.options);

                options.forEach((option, idx) => {
                    if (idx === 0) {
                        option.hidden = false;
                        return;
                    }

                    const trainerId = option.getAttribute('data-trainer-id');
                    const visible = !!selectedTrainerId && trainerId === selectedTrainerId;
                    option.hidden = !visible;

                    if (!visible && option.selected) {
                        memberSelect.value = '';
                    }
                });
            }

            if (trainerSelect) {
                trainerSelect.addEventListener('change', syncMembersByCoach);
            }

            syncMembersByCoach();
        })();
    </script>
@endsection
