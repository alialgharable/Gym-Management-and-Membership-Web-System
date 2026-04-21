@extends('layouts.app')

@section('title', 'Class Details')

@section('content')

    <div class="page-header">
        <div>
            <h1 id="class-title" class="section-title">Loading...</h1>
            <p class="section-subtitle">Class details and booking overview</p>
        </div>

        <div class="actions">
            <a href="{{ route('classes.index') }}" class="btn btn-secondary">Back</a>

            @auth
                @if(auth()->user()->isTrainer() || auth()->user()->isAdmin())
                    <a id="edit-class-btn" href="#" class="btn btn-primary" style="display:none;">Edit</a>
                @endif
            @endauth
        </div>
    </div>

    <div id="class-details-container">
        <div class="card" style="text-align:center; padding:40px;">
            <h3 style="color:#ffd700;">Loading Class Details...</h3>
            <p style="color:#aaa;">Please wait while class details are loaded.</p>
        </div>
    </div>

    @auth
        @if(auth()->user()->isTrainer() || auth()->user()->isAdmin())
            <div id="delete-class-wrap" style="margin-top:1.5rem; display:none;">
                <button id="delete-class-btn" class="btn btn-danger">
                    Delete Class
                </button>
            </div>
        @endif
    @endauth

@endsection

@push('scripts')
<script>
    const classId = @json($classId);
    const isTrainer = @json(auth()->check() && auth()->user()->isTrainer());
    const isAdmin = @json(auth()->check() && auth()->user()->isAdmin());
    const currentUserId = @json(auth()->id());
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

    function bookingStatusColor(status) {
        if (status === 'confirmed') return '#5fd68f';
        if (status === 'cancelled') return '#ff5555';
        return '#ffd700';
    }

    async function loadClassDetails() {
        const container = document.getElementById('class-details-container');
        const title = document.getElementById('class-title');
        const editBtn = document.getElementById('edit-class-btn');
        const deleteWrap = document.getElementById('delete-class-wrap');
        const deleteBtn = document.getElementById('delete-class-btn');

        try {
            const response = await fetch(`/api/classes/${classId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch class details');
            }

            const result = await response.json();
            const gymClass = result.data;

            const bookings = gymClass.bookings || [];
            const bookingsCount = bookings.length;
            const capacity = gymClass.capacity ?? 0;
            const availableSpots = Math.max(capacity - bookingsCount, 0);

            title.textContent = gymClass.name ?? 'Class Details';

            const classOwnerUserId = gymClass?.trainer?.user?.id ?? null;
            const trainerOwnsClass = isTrainer && currentUserId && classOwnerUserId && Number(classOwnerUserId) === Number(currentUserId);
            const canManageThisClass = isAdmin || trainerOwnsClass;

            if (canManageThisClass && editBtn) {
                editBtn.href = `/classes/${gymClass.id}/edit`;
                editBtn.style.display = 'inline-flex';
            }

            if (canManageThisClass && deleteWrap && deleteBtn) {
                deleteWrap.style.display = 'block';

                deleteBtn.onclick = async () => {
                    const runDelete = async () => {
                        try {
                            const response = await fetch(`/api/classes/${gymClass.id}`, {
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
                                    onConfirm: () => {
                                        window.location.href = '/classes';
                                    }
                                });
                            } else {
                                alert(result.message || 'Class deleted successfully!');
                                window.location.href = '/classes';
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
                };
            }

            container.innerHTML = `
                <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                    <div class="card">
                        <h3>Class Information</h3>
                        <p><strong>Category:</strong> ${formatCategory(gymClass.category)}</p>
                        <p><strong>Trainer:</strong> ${gymClass.trainer?.user?.name ?? 'N/A'}</p>
                        <p><strong>Room:</strong> ${gymClass.room?.name ?? 'N/A'}</p>
                        <p><strong>Schedule:</strong> ${formatSchedule(gymClass.schedule)}</p>
                        <p><strong>Capacity:</strong> ${gymClass.capacity ?? 'N/A'} members</p>
                        <p><strong>Description:</strong> ${gymClass.description ?? 'No description available.'}</p>
                    </div>

                    <div class="card">
                        <h3>Booking Overview</h3>
                        <p><strong>Total Bookings:</strong> ${bookingsCount}</p>
                        <p><strong>Available Spots:</strong> ${availableSpots}</p>
                        <p>
                            <strong>Status:</strong>
                            <span style="color: ${availableSpots > 0 ? '#5fd68f' : '#ff5555'}; font-weight:600;">
                                ${availableSpots > 0 ? 'Open for Booking' : 'Full'}
                            </span>
                        </p>
                    </div>
                </div>

                ${bookingsCount
                    ? `
                        <div class="card" style="margin-top:1.5rem;">
                            <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
                                <h3 style="margin:0;">Members Booked</h3>
                                <span style="color:#aaa;">${bookingsCount} total</span>
                            </div>

                            <ul style="list-style:none; padding:0; margin-top:1rem;">
                                ${bookings.map(booking => `
                                    <li style="padding:12px 0; border-bottom:1px solid #2b2b2b;">
                                        <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
                                            <div>
                                                <strong>${booking.member?.user?.name ?? 'N/A'}</strong>
                                                <div style="color:#a9a89d; font-size:0.9rem;">
                                                    ${booking.created_at ? new Date(booking.created_at).toLocaleDateString() : 'N/A'}
                                                </div>
                                            </div>

                                            <span style="color: ${bookingStatusColor(booking.status)}; font-weight:600;">
                                                ${(booking.status ?? 'N/A').charAt(0).toUpperCase() + (booking.status ?? '').slice(1)}
                                            </span>
                                        </div>
                                    </li>
                                `).join('')}
                            </ul>
                        </div>
                    `
                    : ''
                }
            `;
        } catch (error) {
            title.textContent = 'Class Details';

            container.innerHTML = `
                <div class="card" style="text-align:center; padding:40px;">
                    <h3 style="color:#ff6b6b;">Failed to load class details</h3>
                    <p style="color:#aaa;">Please try again.</p>
                </div>
            `;
        }
    }

    loadClassDetails();
</script>
@endpush