@extends('layouts.app')

@section('title', 'Trainers')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Trainers</h1>
            <p class="section-subtitle">Manage gym trainers and their classes</p>
        </div>
    </div>

    <div style="margin-bottom:1rem; display:flex; gap:8px; align-items:center;">
        <form id="trainers-filters" method="GET" action="{{ route('trainers.index') }}" style="display:flex; gap:8px; align-items:center;">
            <input type="text" name="search" placeholder="Search trainers..." value="{{ request('search') }}" style="padding:8px 10px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); background:transparent; color:inherit;">

            <select name="specialty" style="padding:8px 10px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); background:transparent; color:inherit;">
                <option value="">All specialties</option>
                @foreach(\App\Models\Trainer::SPECIALTIES as $key => $label)
                    <option value="{{ $key }}" {{ request('specialty') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <a href="{{ route('trainers.index') }}" class="btn btn-secondary">Reset</a>
        </form>
    </div>

    @if ($trainers->count())
        <div class="card-grid">
            @foreach ($trainers as $trainer)
                @php
                    $trainerUser = $trainer->user;
                    $profileImage = $trainerUser && $trainerUser->profile_picture
                        ? asset('storage/' . $trainerUser->profile_picture)
                        : asset('images/default-avatar.png');
                @endphp

                <div class="card">
                    <div style="display: flex; align-items: center; gap: 0.9rem; margin-bottom: 1rem;">
                        <img
                            src="{{ $profileImage }}"
                            alt="{{ $trainerUser->name ?? 'Trainer' }}"
                            style="width: 56px; height: 56px; border-radius: 50%; object-fit: cover; border: 2px solid #ffd54a;"
                            onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';">

                        <div>
                            <h3 style="margin: 0;">{{ $trainerUser->name ?? 'N/A' }}</h3>
                            <p style="margin: 0.2rem 0 0; font-size: 0.9rem; color: #a9a89d;">
                                Trainer Profile
                            </p>
                        </div>
                    </div>

                    <p><strong>Email:</strong> {{ $trainerUser->email ?? 'N/A' }}</p>
                    <p><strong>Specialization:</strong> {{ $trainer->specialtyLabel() }}</p>
                    <p style="font-size: 0.9rem; color: #a9a89d;">
                        Classes: {{ $trainer->gymClasses->count() }} | Reviews: {{ $trainer->reviews->count() }}
                    </p>

                    <div class="actions" style="margin-top: 1rem;">
                        <a href="{{ route('trainers.show', $trainer) }}" class="btn btn-secondary">View</a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning">
            No trainers found.
        </div>
    @endif
@push('scripts')
    <script>
        (function(){
            const form = document.getElementById('trainers-filters');
            if (!form) return;

            let timer;
            const submit = () => form.submit();

            const search = form.querySelector('input[name="search"]');
            if (search) {
                search.addEventListener('input', function () {
                    clearTimeout(timer);
                    timer = setTimeout(submit, 500);
                });
            }

            form.querySelectorAll('select').forEach(s => s.addEventListener('change', submit));
        })();
    </script>
@endpush
@endsection