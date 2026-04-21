@extends('layouts.app')

@section('title', 'Gym Classes')

@section('content')

    <style>
        .classes-toolbar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .classes-filters {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            width: 100%;
        }

        .classes-filter-input,
        .classes-filter-select {
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

        .classes-filter-input {
            flex: 1 1 280px;
        }

        .classes-filter-select {
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

        .classes-filter-select option {
            background: var(--bg-elevated);
            color: var(--text-main);
        }

        .classes-filter-input::placeholder {
            color: var(--text-muted);
        }

        .classes-filter-input:focus,
        .classes-filter-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(247, 211, 74, 0.14);
        }

        .classes-filter-select::-ms-expand {
            display: none;
        }

        .classes-filter-actions {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-left: auto;
        }

        @media (max-width: 760px) {
            .classes-filter-select,
            .classes-filter-input {
                flex: 1 1 100%;
            }

            .classes-filter-actions {
                width: 100%;
                margin-left: 0;
            }

            .classes-filter-actions .btn {
                flex: 1 1 0;
            }
        }
    </style>

    <div class="page-header">
        <div>
            <h1 class="section-title">Gym Classes</h1>
            <p class="section-subtitle">Manage gym class schedules and trainers</p>
        </div>

        @auth
            @if(auth()->user()->isTrainer() || auth()->user()->isAdmin())
                <a href="{{ route('classes.create') }}" class="btn btn-primary">
                    + New Class
                </a>
            @endif
        @endauth
    </div>

    <div class="classes-toolbar">
        <form id="classes-filters" method="GET" action="{{ route('classes.index') }}" class="classes-filters">
            <input
                type="text"
                name="search"
                placeholder="Search classes..."
                value="{{ request('search') }}"
                class="classes-filter-input"
            >

            <select
                name="category"
                class="classes-filter-select"
            >
                <option value="">All categories</option>
                @foreach(\App\Models\Trainer::SPECIALTIES as $key => $label)
                    <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <div class="classes-filter-actions">
                <a href="{{ route('classes.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div id="classes-container">
        <div class="card" style="text-align:center; padding:40px;">
            <h3 style="color:#ffd700;">Loading Classes...</h3>
            <p style="color:#aaa;">Please wait while classes are loaded.</p>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const isAuthenticated = @json(auth()->check());
    const isMember = @json(auth()->check() && auth()->user()->isMember());
    const isTrainer = @json(auth()->check() && auth()->user()->isTrainer());
    const isAdmin = @json(auth()->check() && auth()->user()->isAdmin());
    const currentUserId = @json(auth()->id());
    const canManageClasses = isTrainer || isAdmin;
    const csrfToken = @json(csrf_token());

    function formatCategory(category) {
        if (!category) return 'N/A';

        const labels = @json(\App\Models\Trainer::SPECIALTIES);

        return labels[category] || category.replaceAll('_', ' ');
    }

    function formatSchedule(schedule) {
        if (!schedule) return 'N/A';

        const date = new Date(schedule);

        if (Number.isNaN(date.getTime())) {
            return schedule;
        }

        return date.toLocaleString();
    }

    function buildActions(cls) {
        let actions = `
            <a href="/classes/${cls.id}" class="btn btn-secondary">View</a>
        `;

        if (isAuthenticated && isMember) {
            actions += `
                <form action="/bookings" method="POST">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="class_id" value="${cls.id}">
                    <button type="submit" class="btn btn-primary">Book</button>
                </form>
            `;
        }

        const classOwnerUserId = cls?.trainer?.user?.id ?? null;
        const trainerOwnsClass = isTrainer && currentUserId && classOwnerUserId && Number(classOwnerUserId) === Number(currentUserId);

        if (isAuthenticated && (isAdmin || trainerOwnsClass)) {
            actions += `
                <a href="/classes/${cls.id}/edit" class="btn btn-secondary">Edit</a>
                <button type="button" class="btn btn-danger btn-delete" data-id="${cls.id}">Delete</button>
            `;
        }

        return actions;
    }

    function attachDeleteEvents() {
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;

                const runDelete = async () => {
                    try {
                        const response = await fetch(`/api/classes/${id}`, {
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

                        const result = await response.json();

                        if (!response.ok) {
                            throw result;
                        }

                        if (window.showModal) {
                            window.showModal({
                                type: 'success',
                                title: 'Deleted',
                                message: result.message || 'Class deleted successfully!',
                                confirmText: 'OK',
                                onConfirm: () => loadClasses()
                            });
                        } else {
                            alert(result.message || 'Class deleted successfully!');
                            loadClasses();
                        }
                    } catch (error) {
                        const message = error.message || 'Failed to delete class.';

                        if (window.showModal) {
                            window.showModal({
                                type: 'error',
                                title: 'Error',
                                message,
                                confirmText: 'OK'
                            });
                        } else {
                            alert(message);
                        }
                    }
                };

                if (window.showModal) {
                    window.showModal({
                        type: 'warning',
                        title: 'Delete Class?',
                        message: 'This class will be removed permanently.',
                        confirmText: 'Yes, Delete',
                        onConfirm: runDelete
                    });
                } else {
                    if (confirm('Delete this class?')) {
                        runDelete();
                    }
                }
            });
        });
    }

    async function loadClasses() {
        const container = document.getElementById('classes-container');
        const form = document.getElementById('classes-filters');

        try {
            const params = new URLSearchParams(new FormData(form)).toString();
            const response = await fetch(`/api/classes?${params}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch classes');
            }

            const result = await response.json();
            const classes = result.data || [];

            if (!classes.length) {
                container.innerHTML = `
                    <div class="card" style="text-align:center; padding:40px;">
                        <h3 style="color:#ffd700;">No Classes Found</h3>
                        <p style="color:#aaa;">There are no gym classes available right now.</p>
                        ${
                            isAuthenticated && canManageClasses
                                ? `<a href="/classes/create" class="btn btn-primary">Create Class</a>`
                                : ''
                        }
                    </div>
                `;
                return;
            }

            container.innerHTML = `
                <div class="card-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
                    ${classes.map(cls => `
                        <div class="card" style="display:flex; flex-direction:column; justify-content:space-between;">
                            <div>
                                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; margin-bottom:0.75rem; flex-wrap:wrap;">
                                    <h3 style="margin:0;">${cls.name ?? 'N/A'}</h3>

                                    <span
                                        style="padding:4px 10px; border-radius:999px; font-size:0.8rem; background:rgba(247, 211, 74, 0.12); color:#f7d34a; white-space:nowrap;"
                                    >
                                        ${formatCategory(cls.category)}
                                    </span>
                                </div>

                                <p><strong>Trainer:</strong> ${cls.trainer?.user?.name ?? 'N/A'}</p>
                                <p><strong>Room:</strong> ${cls.room?.name ?? 'N/A'}</p>
                                <p><strong>Schedule:</strong> ${formatSchedule(cls.schedule)}</p>
                                <p><strong>Capacity:</strong> ${cls.capacity ?? 'N/A'} members</p>

                                <div
                                    style="margin-top:1rem; padding:0.75rem 1rem; border-radius:14px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06);"
                                >
                                    <span style="font-size:0.9rem; color:#a9a89d;">Bookings</span>
                                    <p style="margin:4px 0 0; font-size:1.2rem; font-weight:700; color:#5fd68f;">
                                        ${cls.bookings ? cls.bookings.length : 0}
                                    </p>
                                </div>
                            </div>

                            <div class="actions" style="margin-top:1.25rem; display:flex; flex-wrap:wrap; gap:8px;">
                                ${buildActions(cls)}
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;

            attachDeleteEvents();
        } catch (error) {
            container.innerHTML = `
                <div class="card" style="text-align:center; padding:40px;">
                    <h3 style="color:#ff6b6b;">Failed to load classes</h3>
                    <p style="color:#aaa;">Please try again.</p>
                </div>
            `;
        }
    }

    (function () {
        const form = document.getElementById('classes-filters');
        if (!form) return;

        let timer;

        const search = form.querySelector('input[name="search"]');
        if (search) {
            search.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(loadClasses, 400);
            });
        }

        form.querySelectorAll('select').forEach(select => {
            select.addEventListener('change', loadClasses);
        });
    })();

    loadClasses();
</script>
@endpush