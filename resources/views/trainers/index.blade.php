@extends('layouts.app')

@section('title', 'Trainers')

@section('content')

    <style>
        .trainers-toolbar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .trainers-filters {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            width: 100%;
        }

        .trainers-filter-input,
        .trainers-filter-select {
            min-width: 0;
            height: 46px;
            padding: 0 1rem;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.03);
            color: var(--text-main);
            font: inherit;
            outline: none;
        }

        .trainers-filter-input {
            flex: 1 1 280px;
        }

        .trainers-filter-select {
            flex: 0 0 220px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding-right: 2.6rem;
            background-image: linear-gradient(45deg, transparent 50%, var(--accent) 50%),
                linear-gradient(135deg, var(--accent) 50%, transparent 50%);
            background-position: calc(100% - 18px) 18px, calc(100% - 12px) 18px;
            background-size: 6px 6px, 6px 6px;
            background-repeat: no-repeat;
        }

        .trainers-filter-input::placeholder {
            color: var(--text-muted);
        }

        .trainers-filter-input:focus,
        .trainers-filter-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(247, 211, 74, 0.14);
        }

        .trainers-filter-select option {
            background: var(--bg-elevated);
            color: var(--text-main);
        }

        .trainers-filter-select::-ms-expand {
            display: none;
        }

        .trainers-filter-actions {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-left: auto;
        }

        @media (max-width: 760px) {
            .trainers-filter-input,
            .trainers-filter-select {
                flex: 1 1 100%;
            }

            .trainers-filter-actions {
                width: 100%;
                margin-left: 0;
            }

            .trainers-filter-actions .btn {
                flex: 1 1 0;
            }
        }
    </style>
    <div class="page-header">
        <div>
            <h1 class="section-title">Trainers</h1>
            <p class="section-subtitle">Manage gym trainers and their classes</p>
        </div>
    </div>

    <div class="trainers-toolbar">
        <form id="trainers-filters" method="GET" action="{{ route('trainers.index') }}" class="trainers-filters">
            <input type="text" name="search" placeholder="Search trainers..." value="{{ request('search') }}"
                class="trainers-filter-input">

            <select name="specialty" class="trainers-filter-select">
                <option value="">All specialties</option>
                @foreach(\App\Models\Trainer::SPECIALTIES as $key => $label)
                    <option value="{{ $key }}" {{ request('specialty') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <div class="trainers-filter-actions">
                <a href="{{ route('trainers.index') }}" class="btn btn-secondary">Reset</a>
            </div>
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
            const isAdmin = @json(auth()->check() && auth()->user()->isAdmin());
            const isTrainer = @json(auth()->check() && auth()->user()->isTrainer());
            const currentUserId = @json(auth()->id());
            const csrfToken = @json(csrf_token());

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

            function attachDeleteEvents() {
                document.querySelectorAll('.btn-delete-trainer').forEach(button => {
                    button.addEventListener('click', function () {
                        const trainerId = this.dataset.id;

                        const runDelete = async () => {
                            try {
                                const response = await fetch(`/trainers/${trainerId}`, {
                                    method: 'POST',
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken
                                    },
                                    body: (() => {
                                        const fd = new FormData();
                                        fd.append('_method', 'DELETE');
                                        return fd;
                                    })()
                                });

                                if (!response.ok) {
                                    throw new Error('Failed to delete trainer.');
                                }

                                loadTrainers();
                            } catch (error) {
                                console.error(error);

                                if (window.showModal) {
                                    window.showModal({
                                        type: 'error',
                                        title: 'Error',
                                        message: 'Failed to delete trainer.',
                                        confirmText: 'OK'
                                    });
                                } else {
                                    alert('Failed to delete trainer.');
                                }
                            }
                        };

                        if (window.showModal) {
                            window.showModal({
                                type: 'warning',
                                title: 'Delete Trainer?',
                                message: 'This trainer profile will be removed permanently.',
                                confirmText: 'Delete',
                                onConfirm: runDelete
                            });
                        } else if (confirm('Delete this trainer?')) {
                            runDelete();
                        }
                    });
                });
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
                        const trainerUserId = user.id ?? null;
                        const canManageTrainer = isAdmin || (isTrainer && trainerUserId && currentUserId && Number(trainerUserId) === Number(currentUserId));
                        const canSeeSalary = trainer.salary !== null && trainer.salary !== undefined;
                        const salaryLine = canSeeSalary
                            ? `<p><strong>Salary:</strong> $${Number(trainer.salary).toLocaleString()}</p>`
                            : '';
                        const manageActions = canManageTrainer
                            ? `
                                <a href="/trainers/${trainer.id}/edit" class="btn btn-primary">Edit</a>
                                <button type="button" class="btn btn-danger btn-delete-trainer" data-id="${trainer.id}">Delete</button>
                            `
                            : '';
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
                                    ${salaryLine}
                                    <p style="font-size:0.9rem; color:#a9a89d;">
                                        Classes: ${gymClasses.length} | Reviews: ${reviews.length}
                                    </p>

                                    <div class="actions" style="margin-top:1rem;">
                                        <a href="/trainers/${trainer.id}" class="btn btn-secondary">View</a>
                                        ${manageActions}
                                    </div>
                                </div>
                            `;
                    });

                    attachDeleteEvents();
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