@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')

    <div id="member-dashboard-container">
        <div class="card" style="text-align:center; padding:40px;">
            <h3 style="color:#ffd700;">Loading Dashboard...</h3>
            <p style="color:#aaa;">Please wait while your dashboard is loaded.</p>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const csrfToken = @json(csrf_token());

        function formatDate(value, withTime = false) {
            if (!value) return 'N/A';

            const date = new Date(value);

            if (Number.isNaN(date.getTime())) {
                return value;
            }

            return withTime
                ? date.toLocaleString()
                : date.toLocaleDateString(undefined, {
                    month: 'short',
                    day: '2-digit',
                    year: 'numeric'
                });
        }

        function memberSinceText(value) {
            if (!value) return 'N/A';

            const created = new Date(value);
            if (Number.isNaN(created.getTime())) return 'N/A';

            const now = new Date();
            const diffMs = now - created;
            const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

            if (diffDays < 30) {
                return `${diffDays} days`;
            }

            return `${Math.floor(diffDays / 30)} months`;
        }

        function statusColor(status) {
            if (status === 'confirmed') return '#5fd68f';
            if (status === 'cancelled') return '#ff5555';
            return '#ffd700';
        }

        async function loadMemberDashboard() {
            const container = document.getElementById('member-dashboard-container');

            try {
                const response = await fetch('/api/member/dashboard', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (!response.ok) {
                    throw result;
                }

                const payload = result.data || {};
                const member = payload.member || null;
                const stats = payload.stats || {};
                const activeSubscription = payload.active_subscription || null;
                const bookings = payload.bookings || [];

                if (!member) {
                    container.innerHTML = `
                        <div class="card" style="text-align:center; padding:40px;">
                            <h3 style="color:#ff5555;">Profile Not Found</h3>
                            <p style="color:#aaa;">Please contact support.</p>
                        </div>
                    `;
                    return;
                }

                const memberName = member.user?.name || 'Member';
                const planName = activeSubscription?.plan?.name || null;

                container.innerHTML = `
                    <div class="page-header">
                        <div>
                            <h1 class="section-title">My Dashboard</h1>
                            <p class="section-subtitle">Welcome back, ${memberName}</p>
                        </div>
                    </div>

                    <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">

                        <div class="card" style="background: linear-gradient(135deg, #2e7d32, #1b5e20);">
                            <h3 style="color:#fff;">Subscription</h3>
                            ${
                                activeSubscription && planName
                                    ? `
                                        <p style="color:#a8d5a8; margin:6px 0;"><strong>${planName}</strong></p>
                                        <p style="color:#a8d5a8; font-size:0.9rem;">
                                            Valid until ${formatDate(activeSubscription.end_date)}
                                        </p>
                                    `
                                    : `<p style="color:#ff5555;">No active subscription</p>`
                            }
                        </div>

                        <div class="card" style="background: linear-gradient(135deg, #1976d2, #0d47a1);">
                            <h3 style="color:#fff;">Bookings</h3>
                            <p style="font-size:2.5rem; font-weight:700; color:#fff;">${stats.total_bookings ?? 0}</p>
                            <p style="color:#90caf9; font-size:0.9rem;">Total classes booked</p>
                        </div>

                        <div class="card" style="background: linear-gradient(135deg, #f57c00, #bf360c);">
                            <h3 style="color:#fff;">Confirmed</h3>
                            <p style="font-size:2.5rem; font-weight:700; color:#fff;">${stats.confirmed_bookings ?? 0}</p>
                            <p style="color:#ffb74d; font-size:0.9rem;">Active bookings</p>
                        </div>

                        <div class="card" style="background: linear-gradient(135deg, #c2185b, #880e4f);">
                            <h3 style="color:#fff;">Member Since</h3>
                            <p style="color:#f48fb1;">
                                ${formatDate(member.created_at)}
                            </p>
                            <p style="color:#f48fb1; font-size:0.9rem;">
                                (${memberSinceText(member.created_at)})
                            </p>
                        </div>

                    </div>

                    <div class="card" style="margin-top:1.5rem;">
                        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                            <h3 style="margin:0;">My Bookings</h3>
                            <a href="/bookings/create" class="btn btn-primary">
                                Book Class
                            </a>
                        </div>

                        ${
                            bookings.length
                                ? `
                                    <div style="overflow-x:auto; margin-top:1rem;">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Class</th>
                                                    <th>Trainer</th>
                                                    <th>Schedule</th>
                                                    <th>Status</th>
                                                    <th>Booked</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${bookings.map(booking => `
                                                    <tr>
                                                        <td>${booking.gym_class?.name || 'N/A'}</td>
                                                        <td>${booking.gym_class?.trainer?.user?.name || 'N/A'}</td>
                                                        <td>${formatDate(booking.gym_class?.schedule, true)}</td>
                                                        <td>
                                                            <span style="color:${statusColor(booking.status)};">
                                                                ${booking.status ? booking.status.charAt(0).toUpperCase() + booking.status.slice(1) : 'N/A'}
                                                            </span>
                                                        </td>
                                                        <td>${formatDate(booking.created_at)}</td>
                                                        <td>
                                                            ${
                                                                booking.status === 'confirmed'
                                                                    ? `
                                                                        <button
                                                                            type="button"
                                                                            class="btn btn-danger btn-cancel-booking"
                                                                            data-id="${booking.id}"
                                                                            style="padding:0.4rem 0.8rem;">
                                                                            Cancel
                                                                        </button>
                                                                    `
                                                                    : `<span style="color:#777;">—</span>`
                                                            }
                                                        </td>
                                                    </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                `
                                : `
                                    <p style="color:#aaa; margin-top:1rem;">
                                        No bookings yet.
                                        <a href="/bookings/create" style="color:#f7d34a; font-weight:bold;">
                                            Book a class
                                        </a>
                                    </p>
                                `
                        }
                    </div>
                `;

                attachCancelEvents();
            } catch (error) {
                container.innerHTML = `
                    <div class="card" style="text-align:center; padding:40px;">
                        <h3 style="color:#ff5555;">Failed to load dashboard</h3>
                        <p style="color:#aaa;">Please try again.</p>
                    </div>
                `;
            }
        }

        function attachCancelEvents() {
            document.querySelectorAll('.btn-cancel-booking').forEach(button => {
                button.addEventListener('click', function () {
                    const bookingId = this.dataset.id;

                    const runCancel = async () => {
                        try {
                            const formData = new FormData();
                            formData.append('_method', 'PUT');
                            formData.append('status', 'cancelled');

                            const response = await fetch(`/bookings/${bookingId}`, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: formData
                            });

                            if (!response.ok) {
                                let message = 'Failed to cancel booking.';
                                try {
                                    const result = await response.json();
                                    message = result.message || message;
                                } catch (e) {}
                                throw new Error(message);
                            }

                            loadMemberDashboard();
                        } catch (error) {
                            if (window.showModal) {
                                window.showModal({
                                    type: 'error',
                                    title: 'Error',
                                    message: error.message || 'Failed to cancel booking.',
                                    confirmText: 'OK'
                                });
                            } else {
                                alert(error.message || 'Failed to cancel booking.');
                            }
                        }
                    };

                    if (window.showModal) {
                        window.showModal({
                            type: 'warning',
                            title: 'Cancel Booking?',
                            message: 'This booking will be cancelled.',
                            confirmText: 'Yes, Cancel',
                            onConfirm: runCancel
                        });
                    } else {
                        if (confirm('This booking will be cancelled.')) {
                            runCancel();
                        }
                    }
                });
            });
        }

        loadMemberDashboard();
    </script>
@endpush