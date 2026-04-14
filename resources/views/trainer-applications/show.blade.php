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
                    <a href="{{ route('home') }}" class="btn btn-secondary">← Back</a>
                    <a href="{{ route('trainer-applications.edit', $application) }}" class="btn btn-primary">
                        Edit
                    </a>
                @else
                    <a href="{{ route('trainer-applications.index') }}" class="btn btn-secondary">← Back</a>
                @endif
            @endauth
        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: 1fr 1fr;">

        {{-- Applicant Info --}}
        <div class="card">
            <h3>Applicant Information</h3>
            <p><strong>Name:</strong> {{ $application->user->name ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $application->user->email ?? 'N/A' }}</p>
            <p><strong>Applied On:</strong> {{ $application->created_at->format('M d, Y') }}</p>
        </div>

        {{-- Application Status --}}
        <div class="card">
            <h3>Application Status</h3>

            <p>
                <strong>Status:</strong>
                <span style="color: 
                        {{ $application->status === 'approved' ? '#5fd68f' :
        ($application->status === 'rejected' ? '#ff5555' : '#ffd700') }};">
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
    </div>

    {{-- Admin Review Section --}}
    @auth
        @if(auth()->user()->isAdmin() && $application->status === 'pending')
            <div class="card" style="
                        margin-top: 24px;
                        padding: 28px;
                        border: 1px solid rgba(255, 215, 0, 0.12);
                        border-radius: 22px;
                        background: linear-gradient(180deg, rgba(255,255,255,0.02) 0%, rgba(255,255,255,0.01) 100%);
                        box-shadow: 0 10px 30px rgba(0,0,0,0.25);
                    ">
                <div style="margin-bottom: 22px;">
                    <h3 style="margin: 0; font-size: 1.6rem; color: #ffd700;">Review Application</h3>
                    <p style="margin: 8px 0 0; color: #aaa; font-size: 0.95rem;">
                        Review the trainer application carefully before approving or rejecting it.
                    </p>
                </div>

                <form action="{{ route('trainer-applications.accept', $application) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div style="
                                display: grid;
                                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                                gap: 20px;
                                margin-bottom: 22px;
                            ">
                        <div>
                            <label for="specialty" style="
                                        display: block;
                                        margin-bottom: 8px;
                                        font-weight: 600;
                                        color: #f5f5f5;
                                    ">
                                Specialty
                            </label>

                            <select name="specialty" id="specialty" required style="
                                        width: 100%;
                                        padding: 14px 16px;
                                        border-radius: 14px;
                                        border: 1px solid rgba(255,255,255,0.12);
                                        background: #111;
                                        color: #f5f5f5;
                                        outline: none;
                                    ">
                                <option value="">Select specialty</option>
                                @foreach(\App\Models\Trainer::SPECIALTIES as $value => $label)
                                    <option value="{{ $value }}" {{ old('specialty') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

                            @error('specialty')
                                <div style="color: #ff6b6b; margin-top: 8px; font-size: 0.9rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="bio" style="
                                        display: block;
                                        margin-bottom: 8px;
                                        font-weight: 600;
                                        color: #f5f5f5;
                                    ">
                                Bio
                            </label>

                            <textarea name="bio" id="bio" rows="5" style="
                                        width: 100%;
                                        padding: 14px 16px;
                                        border-radius: 14px;
                                        border: 1px solid rgba(255,255,255,0.12);
                                        background: #111;
                                        color: #f5f5f5;
                                        resize: vertical;
                                        outline: none;
                                    ">{{ old('bio', $application->experience) }}</textarea>

                            @error('bio')
                                <div style="color: #ff6b6b; margin-top: 8px; font-size: 0.9rem;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="
                                display: flex;
                                gap: 14px;
                                flex-wrap: wrap;
                                align-items: center;
                            ">
                        <button type="submit" class="btn btn-success" style="
                                    min-width: 210px;
                                    padding: 14px 22px;
                                    border-radius: 14px;
                                    font-weight: 700;
                                    font-size: 0.98rem;
                                " onclick="return confirm('Approve this application and create trainer account?')">
                            Approve Application
                        </button>
                </form>

                <form action="{{ route('trainer-applications.reject', $application) }}" method="POST" style="margin: 0;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger" style="
                                        min-width: 210px;
                                        padding: 14px 22px;
                                        border-radius: 14px;
                                        font-weight: 700;
                                        font-size: 0.98rem;
                                    " onclick="return confirm('Reject this application?')">
                        Reject Application
                    </button>
                </form>
            </div>
            </div>
        @endif
    @endauth

    {{-- Delete Section --}}
    @auth
        @if(auth()->user()->isAdmin())
            <div class="card" style="
                        margin-top: 20px;
                        padding: 24px 28px;
                        border: 1px solid rgba(255, 80, 80, 0.16);
                        border-radius: 22px;
                        background: linear-gradient(180deg, rgba(255,255,255,0.015) 0%, rgba(255,255,255,0.008) 100%);
                    ">
                <div style="
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            gap: 16px;
                            flex-wrap: wrap;
                        ">
                    <div>
                        <h3 style="margin: 0 0 6px; color: #ff6b6b;">Danger Zone</h3>
                        <p style="margin: 0; color: #aaa;">
                            Permanently remove this application from the system.
                        </p>
                    </div>

                    <form action="{{ route('trainer-applications.destroy', $application) }}" method="POST" style="margin: 0;">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger" style="
                                    padding: 14px 22px;
                                    border-radius: 14px;
                                    font-weight: 700;
                                " onclick="return confirm('This will permanently delete this application. Are you sure?')">
                            Delete Application
                        </button>
                    </form>
                </div>
            </div>
        @endif
    @endauth
@endsection