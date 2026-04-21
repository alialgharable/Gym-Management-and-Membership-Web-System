@extends('layouts.app')

@section('title', 'Trainer Details')

@section('content')
    <div class="page-header">
        <div id="trainer-header" style="display:flex; align-items:center; gap:1rem;"></div>

        <div class="actions" id="trainer-actions">
            <a href="{{ route('trainers.index') }}" class="btn btn-secondary">← Back</a>
        </div>
    </div>

    <div id="trainer-message" style="margin-bottom:1rem;"></div>

    <div id="trainer-content">
        <div class="alert alert-warning">Loading trainer...</div>
    </div>
@endsection

@push('scripts')
<script>
    const trainerId = {{ $trainerId }};
    const csrfToken = '{{ csrf_token() }}';
    const isAdmin = @json(auth()->check() && auth()->user()->isAdmin());
    const isTrainer = @json(auth()->check() && auth()->user()->isTrainer());
    const currentUserId = @json(auth()->id());

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function showMessage(message, type = 'success') {
        const box = document.getElementById('trainer-message');
        const className = type === 'error' ? 'alert alert-warning' : 'alert alert-success';
        box.innerHTML = `<div class="${className}">${escapeHtml(message)}</div>`;

        setTimeout(() => {
            box.innerHTML = '';
        }, 3000);
    }

    function calcAvg(reviews) {
        if (!reviews.length) return 'N/A';
        const avg = reviews.reduce((sum, r) => sum + Number(r.rating || 0), 0) / reviews.length;
        return avg.toFixed(1);
    }

    function renderClasses(classes) {
        if (!classes.length) return '';

        return `
            <div class="card" style="margin-top:1.5rem;">
                <h3>Classes (${classes.length})</h3>
                <ul style="list-style:none; padding:0;">
                    ${classes.map(c => `
                        <li style="padding:10px 0; border-bottom:1px solid #2b2b2b;">
                            <a href="/classes/${c.id}" style="color:#f7d34a; font-weight:600;">
                                ${escapeHtml(c.name)}
                            </a>
                            <span style="color:#a9a89d;">
                                ${escapeHtml(c.schedule || 'N/A')}
                            </span>
                        </li>
                    `).join('')}
                </ul>
            </div>
        `;
    }

    function renderRecentReviews(reviews) {
        if (!reviews.length) return '';

        return `
            <div class="card" style="margin-top:1.5rem;">
                <h3>Recent Reviews</h3>
                <ul style="list-style:none; padding:0;">
                    ${reviews.slice(0, 5).map(r => `
                        <li style="padding:10px 0; border-bottom:1px solid #2b2b2b;">
                            <strong>${escapeHtml(r.member?.name || 'Anonymous')}</strong>
                            <span style="color:#ffd700;">★ ${escapeHtml(r.rating)}/5</span>
                            <p style="margin:5px 0;">
                                ${escapeHtml(r.comment || 'No comment')}
                            </p>
                        </li>
                    `).join('')}
                </ul>
            </div>
        `;
    }

    function renderReviewSection(memberReview) {
        const isMember = @json(auth()->check() && auth()->user()->isMember());

        if (!isMember) return '';

        if (memberReview) {
            return `
                <div class="card" style="margin-top:1.5rem;">
                    <h3>Your Review</h3>
                    <p style="margin-bottom:0.8rem; color:#d7d2ad;">
                        You already reviewed this trainer.
                    </p>

                    <div id="member-review-display">
                        <p><strong>Rating:</strong> <span style="color:#ffd700;">★ ${escapeHtml(memberReview.rating)}/5</span></p>
                        <p><strong>Comment:</strong> ${escapeHtml(memberReview.comment || 'No comment')}</p>

                        <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-top:1rem;">
                            <button type="button" class="btn btn-primary" data-action="edit-review">Edit Review</button>
                            <button type="button" class="btn btn-danger" data-action="delete-review" data-review-id="${memberReview.id}">Delete Review</button>
                        </div>
                    </div>

                    <form id="edit-review-form" data-review-id="${memberReview.id}" style="display:none; margin-top:1rem;">
                        <div class="field-group">
                            <label class="field-label">Rating (1-5) <span style="color:#ff5555;">*</span></label>
                            <input type="number" name="rating" class="field-input" value="${escapeHtml(memberReview.rating)}" min="1" max="5" required>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Comment</label>
                            <textarea name="comment" class="field-input" rows="4" style="resize:vertical;">${escapeHtml(memberReview.comment || '')}</textarea>
                        </div>

                        <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                            <button type="submit" class="btn btn-primary">Update Review</button>
                            <button type="button" class="btn btn-secondary" data-action="cancel-edit">Cancel</button>
                        </div>
                    </form>
                </div>
            `;
        }

        return `
            <div class="card" style="margin-top:1.5rem;">
                <h3>Add Your Review</h3>

                <form id="create-review-form">
                    <div class="field-group">
                        <label class="field-label">Rating (1-5) <span style="color:#ff5555;">*</span></label>
                        <input type="number" name="rating" class="field-input" min="1" max="5" required>
                    </div>

                    <div class="field-group">
                        <label class="field-label">Comment</label>
                        <textarea name="comment" class="field-input" rows="4" style="resize:vertical;"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
        `;
    }

    async function loadTrainer() {
        try {
            const response = await fetch(`/api/trainers/${trainerId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to load trainer');
            }

            const result = await response.json();
            const trainer = result.data;
            const memberReview = result.member_review;
            const user = trainer.user || {};
            const classes = trainer.gym_classes || [];
            const reviews = trainer.reviews || [];
            const canSeeSalary = trainer.salary !== null && trainer.salary !== undefined;
            const salaryLine = canSeeSalary
                ? `<p><strong>Salary:</strong> $${Number(trainer.salary).toLocaleString()}</p>`
                : '';

            const profileImage = user.profile_picture
                ? `/storage/${user.profile_picture}`
                : `/images/default-avatar.png`;
            const trainerUserId = user.id ?? null;
            const canManageTrainer = isAdmin || (isTrainer && trainerUserId && currentUserId && Number(trainerUserId) === Number(currentUserId));

            document.getElementById('trainer-header').innerHTML = `
                <img
                    src="${profileImage}"
                    alt="${escapeHtml(user.name || 'Trainer')}"
                    style="width:70px; height:70px; border-radius:50%; object-fit:cover; border:2px solid #ffd54a;"
                    onerror="this.onerror=null; this.src='/images/default-avatar.png';">

                <div>
                    <h1 class="section-title" style="margin:0;">${escapeHtml(user.name || 'Trainer')}</h1>
                    <p class="section-subtitle" style="margin:0.2rem 0 0;">Trainer Profile</p>
                </div>
            `;

            if (canManageTrainer) {
                const actions = document.getElementById('trainer-actions');

                if (actions) {
                    actions.innerHTML = `
                        <a href="{{ route('trainers.index') }}" class="btn btn-secondary">← Back</a>
                        <a href="/trainers/${trainer.id}/edit" class="btn btn-primary">Edit</a>
                        <button type="button" class="btn btn-danger" id="delete-trainer-btn">Delete</button>
                    `;

                    const deleteBtn = document.getElementById('delete-trainer-btn');
                    if (deleteBtn) {
                        deleteBtn.addEventListener('click', function () {
                            const runDelete = async () => {
                                try {
                                    const response = await fetch(`/trainers/${trainer.id}`, {
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

                                    window.location.href = '/trainers';
                                } catch (error) {
                                    showMessage(error.message || 'Failed to delete trainer.', 'error');
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
                    }
                }
            }

            document.getElementById('trainer-content').innerHTML = `
                <div class="card-grid" style="grid-template-columns:1fr 1fr;">
                    <div class="card">
                        <h3>Personal Information</h3>
                        <p><strong>Name:</strong> ${escapeHtml(user.name || 'N/A')}</p>
                        <p><strong>Email:</strong> ${escapeHtml(user.email || 'N/A')}</p>
                        <p><strong>Specialization:</strong> ${escapeHtml(trainer.specialty_label || trainer.specialty || 'N/A')}</p>
                        ${salaryLine}
                    </div>

                    <div class="card">
                        <h3>Statistics</h3>
                        <p><strong>Classes:</strong> ${classes.length}</p>
                        <p><strong>Reviews:</strong> ${reviews.length}</p>
                        <p><strong>Avg Rating:</strong> ${calcAvg(reviews)}/5</p>
                    </div>
                </div>

                ${renderReviewSection(memberReview)}
                ${renderClasses(classes)}
                ${renderRecentReviews(reviews)}
            `;
        } catch (error) {
            document.getElementById('trainer-content').innerHTML =
                `<div class="alert alert-warning">Failed to load trainer.</div>`;
            console.error(error);
        }
    }

    function showEditReviewForm() {
        const form = document.getElementById('edit-review-form');
        const display = document.getElementById('member-review-display');

        if (form) form.style.display = 'block';
        if (display) display.style.display = 'none';
    }

    function hideEditReviewForm() {
        const form = document.getElementById('edit-review-form');
        const display = document.getElementById('member-review-display');

        if (form) form.style.display = 'none';
        if (display) display.style.display = 'block';
    }

    async function submitCreateReview(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);

        const payload = {
            trainer_id: trainerId,
            rating: formData.get('rating'),
            comment: formData.get('comment')
        };

        try {
            const response = await fetch('/api/reviews', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to submit review');
            }

            showMessage(result.message || 'Review submitted successfully');
            await loadTrainer();
        } catch (error) {
            showMessage(error.message || 'Failed to submit review', 'error');
            console.error(error);
        }
    }

    async function submitEditReview(event, reviewId) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);

        const payload = {
            rating: formData.get('rating'),
            comment: formData.get('comment')
        };

        try {
            const response = await fetch(`/api/reviews/${reviewId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to update review');
            }

            showMessage(result.message || 'Review updated successfully');
            await loadTrainer();
        } catch (error) {
            showMessage(error.message || 'Failed to update review', 'error');
            console.error(error);
        }
    }

    async function deleteReview(reviewId) {
        if (!confirm('Delete your review?')) {
            return;
        }

        try {
            const response = await fetch(`/api/reviews/${reviewId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to delete review');
            }

            showMessage(result.message || 'Review deleted successfully');
            await loadTrainer();
        } catch (error) {
            showMessage(error.message || 'Failed to delete review', 'error');
            console.error(error);
        }
    }

    // Event delegation: handle clicks for edit/delete and submit for dynamic forms
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;

        const action = btn.getAttribute('data-action');

        if (action === 'edit-review') {
            showEditReviewForm();
            return;
        }

        if (action === 'delete-review') {
            const reviewId = btn.getAttribute('data-review-id');
            if (reviewId) deleteReview(reviewId);
            return;
        }

        if (action === 'cancel-edit') {
            hideEditReviewForm();
            return;
        }
    });

    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (form.id === 'create-review-form') {
            submitCreateReview(e);
            return;
        }

        if (form.id === 'edit-review-form') {
            const reviewId = form.getAttribute('data-review-id');
            submitEditReview(e, reviewId);
            return;
        }
    });

    loadTrainer();
</script>
@endpush