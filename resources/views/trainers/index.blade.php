@extends('layouts.app')

@section('title', 'Trainers')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Trainers</h1>
            <p class="section-subtitle">Manage gym trainers and their classes</p>
        </div>
    </div>

    <div style="margin-bottom:1rem; display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
        <form id="trainers-filters" method="GET" action="{{ route('trainers.index') }}"
            style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; width:100%;">
            <input type="text" name="search" placeholder="Search trainers..." value="{{ request('search') }}"
                style="padding:8px 10px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); background:transparent; color:inherit;">

            <select name="specialty"
                style="padding:8px 10px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); background:#111; color:inherit;">
                <option value="">All specialties</option>
                @foreach(\App\Models\Trainer::SPECIALTIES as $key => $label)
                    <option value="{{ $key }}" {{ request('specialty') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <a href="{{ route('trainers.index') }}" class="btn btn-secondary">Reset</a>
        </form>
    </div>

    <div id="trainers-loading" class="alert alert-warning" style="display:none;">
        Loading trainers...
    </div>

    <div id="trainers-error" class="alert alert-warning" style="display:none;">
        Failed to load trainers.
    </div>

    <div id="trainers-empty" class="alert alert-warning" style="display:none;">
        No trainers found.
    </div>

    <div id="trainers-container" class="card-grid"></div>
@endsection

@push('scripts')
    <script>
        (function () {
            const form = document.getElementById('trainers-filters');
            const container = document.getElementById('trainers-container');
            const loadingBox = document.getElementById('trainers-loading');
            const errorBox = document.getElementById('trainers-error');
            const emptyBox = document.getElementById('trainers-empty');

            if (!form || !container) return;

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function specialtyLabel(key) {
                const specialties = @json(\App\Models\Trainer::SPECIALTIES);
                return specialties[key] ?? key ?? 'N/A';
            }

            function showLoading() {
                loadingBox.style.display = 'block';
                errorBox.style.display = 'none';
                emptyBox.style.display = 'none';
            }

            function hideLoading() {
                loadingBox.style.display = 'none';
            }

            function showError() {
                errorBox.style.display = 'block';
                emptyBox.style.display = 'none';
            }

            function showEmpty() {
                emptyBox.style.display = 'block';
                errorBox.style.display = 'none';
            }

            function hideMessages() {
                errorBox.style.display = 'none';
                emptyBox.style.display = 'none';
            }

            async function loadTrainers() {
                showLoading();
                hideMessages();
                container.innerHTML = '';

                try {
                    const params = new URLSearchParams(new FormData(form));
                    const response = await fetch(`/api/trainers?${params.toString()}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to fetch trainers');
                    }

                    const result = await response.json();
                    const trainers = result.data || [];

                    hideLoading();

                    if (!trainers.length) {
                        showEmpty();
                        return;
                    }

                    trainers.forEach(trainer => {
                        const user = trainer.user || {};
                        const gymClasses = trainer.gym_classes || [];
                        const reviews = trainer.reviews || [];
                        const profileImage = user.profile_picture
                            ? `/storage/${user.profile_picture}`
                            : `/images/default-avatar.png`;

                        container.innerHTML += `
                                <div class="card">
                                    <div style="display:flex; align-items:center; gap:0.9rem; margin-bottom:1rem;">
                                        <img
                                            src="${profileImage}"
                                            alt="${escapeHtml(user.name || 'Trainer')}"
                                            style="width:56px; height:56px; border-radius:50%; object-fit:cover; border:2px solid #ffd54a;"
                                            onerror="this.onerror=null; this.src='/images/default-avatar.png';">

                                        <div>
                                            <h3 style="margin:0;">${escapeHtml(user.name || 'N/A')}</h3>
                                            <p style="margin:0.2rem 0 0; font-size:0.9rem; color:#a9a89d;">
                                                Trainer Profile
                                            </p>
                                        </div>
                                    </div>

                                    <p><strong>Email:</strong> ${escapeHtml(user.email || 'N/A')}</p>
                                   <p><strong>Specialization:</strong> ${escapeHtml(trainer.specialty_label || trainer.specialty || 'N/A')}</p>
                                    <p style="font-size:0.9rem; color:#a9a89d;">
                                        Classes: ${gymClasses.length} | Reviews: ${reviews.length}
                                    </p>

                                    <div class="actions" style="margin-top:1rem;">
                                        <a href="/trainers/${trainer.id}" class="btn btn-secondary">View</a>
                                    </div>
                                </div>
                            `;
                    });
                } catch (error) {
                    hideLoading();
                    container.innerHTML = '';
                    showError();
                    console.error(error);
                }
            }

            let timer;
            const searchInput = form.querySelector('input[name="search"]');
            const selects = form.querySelectorAll('select');

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(timer);
                    timer = setTimeout(() => {
                        const params = new URLSearchParams(new FormData(form));
                        window.history.replaceState({}, '', `${form.action}?${params.toString()}`);
                        loadTrainers();
                    }, 500);
                });
            }

            selects.forEach(select => {
                select.addEventListener('change', function () {
                    const params = new URLSearchParams(new FormData(form));
                    window.history.replaceState({}, '', `${form.action}?${params.toString()}`);
                    loadTrainers();
                });
            });

            loadTrainers();
        })();
    </script>
@endpush