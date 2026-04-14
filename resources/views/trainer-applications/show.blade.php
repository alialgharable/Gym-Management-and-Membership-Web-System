@extends('layouts.app')

@section('title', 'Application Details')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="section-title">{{ $application->user->name ?? 'Applicant' }}'s Application</h1>
            <p class="section-subtitle">Application #{{ $application->id }}</p>
        </div>

        <div class="actions">
            @auth
                @if(auth()->id() === $application->user_id)
                    <a href="{{ route('home') }}" class="btn btn-secondary">Back</a>
                    <a href="{{ route('trainer-applications.edit', $application) }}" class="btn btn-primary">
                        Edit
                    </a>
                @else
                    <a href="{{ route('trainer-applications.index') }}" class="btn btn-secondary">Back</a>
                @endif
            @endauth
        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">

        <div class="card">
            <h3>Applicant Information</h3>
            <p><strong>Name:</strong> {{ $application->user->name ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $application->user->email ?? 'N/A' }}</p>
            <p><strong>Applied:</strong> {{ $application->created_at->format('M d, Y') }}</p>
        </div>

        <div class="card">
            <h3>Application Status</h3>

            <p>
                <strong>Status:</strong>
                <span style="font-weight:600; color:
                    {{ $application->status === 'approved' ? '#5fd68f' :
                       ($application->status === 'rejected' ? '#ff5555' : '#ffd700') }};">
                    {{ ucfirst($application->status) }}
                </span>
            </p>

            <p><strong>Experience:</strong> {{ $application->experience ?? 'N/A' }}</p>

            <p style="margin-top:1rem;">
                <strong>CV:</strong>
                @if($application->cv_file)
                    <a href="{{ asset('storage/' . $application->cv_file) }}" target="_blank" class="btn btn-secondary" style="margin-left:10px;">
                        View
                    </a>
                @else
                    <span style="color:#aaa;">No file</span>
                @endif
            </p>
        </div>

    </div>

    @auth
        @if(auth()->user()->isAdmin() && $application->status === 'pending')
            <div class="card" style="margin-top:1.5rem;">
                <div style="margin-bottom:1.5rem;">
                    <h3 style="margin:0;">Review Application</h3>
                    <p style="margin-top:6px; color:#aaa;">
                        Approve or reject this trainer request
                    </p>
                </div>

                <form action="{{ route('trainer-applications.accept', $application) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:16px;">

                        <div>
                            <label class="field-label">Specialty</label>
                            <select name="specialty" class="field-select" required>
                                <option value="">Select specialty</option>
                                @foreach(\App\Models\Trainer::SPECIALTIES as $value => $label)
                                    <option value="{{ $value }}" @selected(old('specialty') == $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('specialty')
                                <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="field-label">Bio</label>
                            <textarea
                                name="bio"
                                rows="4"
                                class="field-input"
                                style="resize:vertical;"
                            >{{ old('bio', $application->experience) }}</textarea>
                            @error('bio')
                                <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:1.5rem;">
                        <button type="submit" class="btn btn-success btn-approve">
                            Approve Application
                        </button>
                </form>

                <form action="{{ route('trainer-applications.reject', $application) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <button type="button" class="btn btn-danger btn-reject">
                        Reject Application
                    </button>
                </form>

                    </div>
            </div>
        @endif
    @endauth

    @auth
        @if(auth()->user()->isAdmin())
            <div class="card" style="margin-top:1.5rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                    <div>
                        <h3 style="margin:0; color:#ff5555;">Danger Zone</h3>
                        <p style="color:#aaa; margin:4px 0 0;">Delete this application permanently</p>
                    </div>

                    <form action="{{ route('trainer-applications.destroy', $application) }}" method="POST" class="delete-form">
                        @csrf
                        @method('DELETE')

                        <button type="button" class="btn btn-danger btn-delete">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @endif
    @endauth

@endsection

@push('scripts')
<script>
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const form = this.closest('form');
            window.showModal({
                type: 'warning',
                title: 'Delete Application?',
                message: 'This will permanently remove the application.',
                confirmText: 'Delete',
                onConfirm: () => form.submit()
            });
        });
    });

    document.querySelectorAll('.btn-approve').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('form');
            window.showModal({
                type: 'success',
                title: 'Approve Application?',
                message: 'This will create a trainer account.',
                confirmText: 'Approve',
                onConfirm: () => form.submit()
            });
        });
    });

    document.querySelectorAll('.btn-reject').forEach(button => {
        button.addEventListener('click', function () {
            const form = this.closest('form');
            window.showModal({
                type: 'warning',
                title: 'Reject Application?',
                message: 'This action cannot be undone.',
                confirmText: 'Reject',
                onConfirm: () => form.submit()
            });
        });
    });
</script>
@endpush