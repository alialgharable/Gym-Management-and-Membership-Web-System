@extends('layouts.app')

@section('title', 'Gym Classes')

@section('content')

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

    <div style="margin-bottom:1rem; display:flex; gap:8px; align-items:center;">
        <form id="classes-filters" method="GET" action="{{ route('classes.index') }}" style="display:flex; gap:8px; align-items:center;">
            <input type="text" name="search" placeholder="Search classes..." value="{{ request('search') }}" style="padding:8px 10px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); background:transparent; color:inherit;">

            <select name="category" style="padding:8px 10px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); background:transparent; color:inherit;">
                <option value="">All categories</option>
                @foreach($categories ?? ['combat'=>'Combat Sports','yoga_pilates'=>'Yoga & Pilates','group_training'=>'Group Training','fitness_machines'=>'Fitness Machines'] as $key => $label)
                    <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select name="room_id" style="padding:8px 10px; border-radius:8px; border:1px solid rgba(255,255,255,0.06); background:transparent; color:inherit;">
                <option value="">All rooms</option>
                @foreach(\App\Models\Room::all() as $r)
                    <option value="{{ $r->id }}" {{ request('room_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                @endforeach
            </select>

            <a href="{{ route('classes.index') }}" class="btn btn-secondary">Reset</a>
        </form>
    </div>

    @if ($classes->count())
        <div class="card-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
            @foreach ($classes as $class)
                <div class="card" style="display:flex; flex-direction:column; justify-content:space-between;">
                    <div>
                        <div
                            style="display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; margin-bottom:0.75rem; flex-wrap:wrap;">
                            <h3 style="margin:0;">
                                {{ $class->name }}
                            </h3>

                            <span
                                style="padding:4px 10px; border-radius:999px; font-size:0.8rem; background:rgba(247, 211, 74, 0.12); color:#f7d34a; white-space:nowrap;">
                                {{ str_replace('_', ' ', ucfirst($class->category ?? 'N/A')) }}
                            </span>
                        </div>

                        <p><strong>Trainer:</strong> {{ $class->trainer->user->name ?? 'N/A' }}</p>
                        <p><strong>Room:</strong> {{ $class->room->name ?? 'N/A' }}</p>
                        <p><strong>Schedule:</strong> {{ $class->schedule ?? 'N/A' }}</p>
                        <p><strong>Capacity:</strong> {{ $class->capacity ?? 'N/A' }} members</p>

                        <div
                            style="margin-top:1rem; padding:0.75rem 1rem; border-radius:14px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06);">
                            <span style="font-size:0.9rem; color:#a9a89d;">Bookings</span>
                            <p style="margin:4px 0 0; font-size:1.2rem; font-weight:700; color:#5fd68f;">
                                {{ $class->bookings->count() }}
                            </p>
                        </div>
                    </div>

                    <div class="actions" style="margin-top:1.25rem; display:flex; flex-wrap:wrap; gap:8px;">
                        <a href="{{ route('classes.show', $class) }}" class="btn btn-secondary">
                            View
                        </a>

                        @auth
                            @if(auth()->user()->isMember())
                                <form action="{{ route('bookings.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="class_id" value="{{ $class->id }}">

                                    <button type="submit" class="btn btn-primary">
                                        Book
                                    </button>
                                </form>
                            @endif
                        @endauth

                        @auth
                            @if(auth()->user()->isTrainer() || auth()->user()->isAdmin())
                                <a href="{{ route('classes.edit', $class) }}" class="btn btn-secondary">
                                    Edit
                                </a>

                                <form action="{{ route('classes.destroy', $class) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')

                                    <button type="button" class="btn btn-danger btn-delete">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card" style="text-align:center; padding:40px;">
            <h3 style="color:#ffd700;">No Classes Found</h3>
            <p style="color:#aaa;">There are no gym classes available right now.</p>

            @auth
                @if(auth()->user()->isTrainer() || auth()->user()->isAdmin())
                    <a href="{{ route('classes.create') }}" class="btn btn-primary">
                        Create Class
                    </a>
                @endif
            @endauth
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {
                const form = this.closest('form');

                window.showModal({
                    type: 'warning',
                    title: 'Delete Class?',
                    message: 'This class will be removed permanently.',
                    confirmText: 'Yes, Delete',
                    onConfirm: () => form.submit()
                });
            });
        });
    </script>
@endpush

@push('scripts')
    <script>
        (function(){
            const form = document.getElementById('classes-filters');
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