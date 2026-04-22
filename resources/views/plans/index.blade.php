@extends('layouts.app')

@section('title', 'Membership Plans')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="section-title">Membership Plans</h1>
            <p class="section-subtitle">Choose the perfect plan for your fitness journey</p>
            <div id="tier-switcher" style="display:flex; gap:8px; margin-top:12px;">
                <button id="tier-basic-btn" type="button" class="btn btn-secondary">Basic</button>
                <button id="tier-premium-btn" type="button" class="btn btn-secondary">Premium</button>
            </div>
        </div>

        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ route('plans.create') }}" class="btn btn-primary">
                    + Create Plan
                </a>
            @endif
        @endauth
    </div>

    <div id="plans-list-container">
        @auth
            @if(auth()->user()->isMember())
                @php
                    $__memberActiveSubscription = auth()->user()->member
                        ? auth()->user()->member->subscription()
                            ->with('plan')
                            ->where('status', 'active')
                            ->whereDate('end_date', '>=', now()->toDateString())
                            ->latest('end_date')
                            ->first()
                        : null;
                @endphp

                <div id="active-subscription-panel" class="card" style="margin-bottom:1rem; {{ $__memberActiveSubscription ? '' : 'display:none;' }}">
                    <h3 style="margin-bottom:8px;">Current Subscription</h3>
                    <p id="active-subscription-text" style="color:#d7d2ad; margin-bottom:12px;">
                        {{ $__memberActiveSubscription
                            ? (($__memberActiveSubscription->plan->name ?? 'Plan') . ' (' . ucfirst((string)($__memberActiveSubscription->plan->tier ?? 'basic')) . ') until ' . optional($__memberActiveSubscription->end_date)->format('M d, Y'))
                            : '' }}
                    </p>
                    <button id="cancel-active-subscription-btn" type="button" class="btn btn-danger">Cancel Current Subscription</button>
                </div>
            @endif
        @endauth

        <div id="plans-list" class="card-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
            <!-- Plans will be rendered here by JS -->
        </div>
    </div>

    @auth
        @if(auth()->user()->isMember())
            <div id="premium-coach-section" class="card" style="margin-top:1.5rem; display:none;">
                <h3 style="margin-bottom:10px;">Premium Coach Selection</h3>
                <p style="margin-bottom:14px; color:#c8c5b4;">Choose a trainer for your premium plan request. The trainer will receive a notification and must approve.</p>

                <form id="premium-subscribe-form" style="display:grid; gap:12px; max-width:700px;">
                    <input type="hidden" id="premium-plan-id" name="plan_id">

                    <div class="field-group" style="margin:0;">
                        <label class="field-label" for="premium-plan-label">Selected Plan</label>
                        <input id="premium-plan-label" class="field-input" type="text" disabled>
                    </div>

                    <div class="field-group" style="margin:0;">
                        <label class="field-label" for="premium-trainer-select">Trainer</label>
                        <select id="premium-trainer-select" name="trainer_id" class="field-select" required>
                            <option value="">Select trainer</option>
                        </select>
                    </div>

                    <div class="field-group" style="margin:0;">
                        <label class="field-label" for="premium-member-note">Note (optional)</label>
                        <textarea id="premium-member-note" name="member_note" class="field-input" rows="3" placeholder="Share your goals, schedule, or preferences"></textarea>
                    </div>

                    <div class="actions" style="display:flex; gap:8px;">
                        <button id="premium-submit-btn" class="btn btn-primary" type="submit">Subscribe & Send Request</button>
                    </div>
                </form>
            </div>
        @endif
    @endauth

@endsection

@push('scripts')
    <script>
        (function () {
            const apiBase = '/api/plans';
            const container = document.getElementById('plans-list');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const tierBasicBtn = document.getElementById('tier-basic-btn');
            const tierPremiumBtn = document.getElementById('tier-premium-btn');
            const premiumSection = document.getElementById('premium-coach-section');
            const premiumForm = document.getElementById('premium-subscribe-form');
            const premiumPlanId = document.getElementById('premium-plan-id');
            const premiumPlanLabel = document.getElementById('premium-plan-label');
            const premiumTrainerSelect = document.getElementById('premium-trainer-select');
            const premiumMemberNote = document.getElementById('premium-member-note');
            const activeSubscriptionPanel = document.getElementById('active-subscription-panel');
            const activeSubscriptionText = document.getElementById('active-subscription-text');
            const cancelActiveSubscriptionBtn = document.getElementById('cancel-active-subscription-btn');

            @php
                $__activePlanId = null;
                $__activePlanTier = null;
                $__activePlanName = null;
                $__activePlanEndDate = null;

                if (auth()->check() && auth()->user()->isMember() && auth()->user()->member) {
                    $__activePlan = auth()->user()->member->subscription()
                        ->where('status', 'active')
                        ->whereDate('end_date', '>=', now()->toDateString())
                        ->latest()
                        ->first();

                    $__activePlanId = $__activePlan?->membership_plan_id;
                    $__activePlanTier = $__activePlan?->plan?->tier;
                    $__activePlanName = $__activePlan?->plan?->name;
                    $__activePlanEndDate = $__activePlan?->end_date;
                }

                $__plans_user = [
                    'isAdmin' => auth()->check() && auth()->user()->isAdmin(),
                    'isTrainer' => auth()->check() && auth()->user()->isTrainer(),
                    'isMember' => auth()->check() && auth()->user()->isMember(),
                    'isAuth' => auth()->check(),
                    'activePlanId' => $__activePlanId,
                    'activePlanTier' => $__activePlanTier,
                    'activePlanName' => $__activePlanName,
                    'activePlanEndDate' => $__activePlanEndDate,
                ];
            @endphp

            const user = {!! json_encode($__plans_user) !!};

            const subscriptionsStore = '{{ route('subscriptions.store') }}';
            const cancelActiveSubscriptionUrl = '{{ route('subscriptions.cancel-active') }}';
            let currentTier = 'basic';
            let trainersLoaded = false;

            function showAppModal({ title = 'Message', message = '', confirmText = 'OK', cancelText = null, onConfirm = null }) {
                if (typeof window.showModal === 'function') {
                    try { window.showModal({ title, message, confirmText, cancelText, onConfirm }); return; } catch (e) { /* fallthrough */ }
                }

                // fallback: manipulate globalModal directly
                const modal = document.getElementById('globalModal');
                if (!modal) {
                    alert(message);
                    return;
                }

                document.getElementById('modalTitle').textContent = title;
                document.getElementById('modalMessage').textContent = message;

                const confirmBtn = document.getElementById('modalConfirmBtn');
                const cancelBtn = document.getElementById('modalCancelBtn');

                confirmBtn.textContent = confirmText;
                confirmBtn.onclick = () => {
                    modal.classList.remove('show');
                    if (onConfirm) onConfirm();
                };

                if (cancelText) {
                    cancelBtn.style.display = 'inline-block';
                    cancelBtn.textContent = cancelText;
                    cancelBtn.onclick = () => modal.classList.remove('show');
                } else {
                    cancelBtn.style.display = 'none';
                }

                modal.classList.add('show');
            }

            function buildCard(plan) {
                const card = document.createElement('div');
                card.className = 'card';
                card.style.display = 'flex';
                card.style.flexDirection = 'column';
                card.style.justifyContent = 'space-between';

                const inner = document.createElement('div');

                const h3 = document.createElement('h3');
                h3.style.color = '#f7d34a';
                h3.style.marginBottom = '10px';
                h3.textContent = (plan.duration_label || plan.name || 'Plan');

                const priceWrap = document.createElement('div');
                priceWrap.style.marginBottom = '10px';

                const price = document.createElement('span');
                price.style.fontSize = '2rem';
                price.style.fontWeight = '700';
                price.style.color = String(user.activePlanId) === String(plan.id) ? '#5fd68f' : '#ffffff';
                price.textContent = '$' + Number(plan.price).toFixed(2);

                const dur = document.createElement('span');
                dur.style.color = '#aaa';
                dur.style.fontSize = '0.9rem';
                dur.textContent = ' / ' + (plan.duration_label || 'N/A');

                priceWrap.appendChild(price);
                priceWrap.appendChild(dur);

                const desc = document.createElement('p');
                desc.style.color = '#d7d2ad';
                desc.style.lineHeight = '1.6';
                desc.style.minHeight = '60px';
                desc.textContent = plan.description || 'No description available for this plan.';

                inner.appendChild(h3);
                inner.appendChild(priceWrap);
                inner.appendChild(desc);

                const actions = document.createElement('div');
                actions.className = 'actions';
                actions.style.marginTop = '20px';
                actions.style.display = 'flex';
                actions.style.flexWrap = 'wrap';
                actions.style.gap = '8px';

                const view = document.createElement('a');
                view.className = 'btn btn-secondary';
                view.href = '/plans/' + plan.id;
                view.textContent = 'View';
                actions.appendChild(view);

                if (user.isMember && !user.isAdmin && !user.isTrainer) {
                    const btn = document.createElement('button');
                    btn.className = 'btn btn-primary';
                    btn.textContent = 'Subscribe';
                    btn.type = 'button';
                    btn.addEventListener('click', async () => {
                        if (currentTier === 'premium') {
                            if (!premiumSection || !premiumPlanId || !premiumPlanLabel) {
                                showAppModal({ title: 'Premium', message: 'Premium coach section is unavailable.', confirmText: 'OK' });
                                return;
                            }

                            premiumPlanId.value = String(plan.id);
                            premiumPlanLabel.value = `${plan.duration_label || plan.name} - $${Number(plan.price).toFixed(2)}`;
                            premiumSection.style.display = 'block';

                            await loadTrainers();

                            premiumSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            return;
                        }

                        btn.disabled = true;
                        try {
                            const res = await fetch(subscriptionsStore, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrf,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ plan_id: plan.id })
                            });

                            const json = await res.json().catch(() => ({}));
                            const msg = json.message || (res.ok ? 'Subscribed successfully' : 'Subscribe failed');
                            showAppModal({ title: res.ok ? 'Subscribed' : 'Subscription', message: msg, confirmText: 'OK' });
                        } catch (err) {
                            console.error(err);
                            const msg = err.message || 'Subscribe failed';
                            showAppModal({ title: 'Subscription', message: msg, confirmText: 'OK' });
                        } finally {
                            btn.disabled = false;
                        }
                    });

                    actions.appendChild(btn);
                }

                if (user.isAdmin) {
                    const edit = document.createElement('a');
                    edit.className = 'btn btn-secondary';
                    edit.href = '/plans/' + plan.id + '/edit';
                    edit.textContent = 'Edit';
                    actions.appendChild(edit);

                    const delBtn = document.createElement('button');
                    delBtn.className = 'btn btn-danger';
                    delBtn.textContent = 'Delete';
                    delBtn.addEventListener('click', () => {
                        showAppModal({ title: 'Delete Plan?', message: 'This action cannot be undone.', confirmText: 'Yes, Delete', onConfirm: () => deletePlan(plan.id, card) });
                    });
                    actions.appendChild(delBtn);
                }

                card.appendChild(inner);
                card.appendChild(actions);

                return card;
            }

            async function deletePlan(id, cardEl) {
                try {
                    const res = await fetch('/api/plans/' + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        }
                    });

                    if (!res.ok) throw new Error('Delete failed');

                    cardEl.remove();
                } catch (err) {
                    console.error(err);
                    showAppModal({ title: 'Delete', message: 'Could not delete plan.', confirmText: 'OK' });
                }
            }

            async function loadTrainers() {
                if (!premiumTrainerSelect || trainersLoaded) {
                    return;
                }

                try {
                    const res = await fetch('/api/trainers', { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) throw new Error('Failed to load trainers');

                    const json = await res.json();
                    const trainers = json.data || [];

                    trainers.forEach((trainer) => {
                        const option = document.createElement('option');
                        option.value = trainer.id;
                        option.textContent = `${trainer.user?.name || 'Trainer'} - ${trainer.specialty_label || trainer.specialty || 'Specialty'}`;
                        premiumTrainerSelect.appendChild(option);
                    });

                    trainersLoaded = true;
                } catch (error) {
                    console.error(error);
                    showAppModal({ title: 'Trainers', message: 'Could not load trainers list.', confirmText: 'OK' });
                }
            }

            async function load() {
                container.innerHTML = '';
                try {
                    const res = await fetch(`${apiBase}?tier=${encodeURIComponent(currentTier)}`, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) throw new Error('Failed to load plans');
                    const json = await res.json();
                    const plans = json.data || [];

                    if (!plans.length) {
                        const card = document.createElement('div');
                        card.className = 'card';
                        card.style.textAlign = 'center';
                        card.style.padding = '40px';
                        card.innerHTML = '<h3 style="color:#ffd700;">No Plans Available</h3><p style="color:#aaa;">Start by creating your first membership plan.</p>';
                        if (user.isAdmin) {
                            const a = document.createElement('a');
                            a.className = 'btn btn-primary';
                            a.href = '{{ route('plans.create') }}';
                            a.textContent = '+ Create Plan';
                            card.appendChild(a);
                        }
                        container.appendChild(card);
                        return;
                    }

                    plans.forEach(p => container.appendChild(buildCard(p)));
                } catch (err) {
                    console.error(err);
                    container.textContent = 'Error loading plans.';
                }
            }

            function updateTierButtons() {
                if (tierBasicBtn) {
                    tierBasicBtn.className = currentTier === 'basic' ? 'btn btn-primary' : 'btn btn-secondary';
                }

                if (tierPremiumBtn) {
                    tierPremiumBtn.className = currentTier === 'premium' ? 'btn btn-primary' : 'btn btn-secondary';
                }

                if (premiumSection) {
                    if (currentTier === 'premium' && user.isMember) {
                        premiumSection.style.display = premiumPlanId && premiumPlanId.value ? 'block' : 'none';
                    } else {
                        premiumSection.style.display = 'none';
                    }
                }
            }

            tierBasicBtn?.addEventListener('click', () => {
                currentTier = 'basic';
                updateTierButtons();
                load();
            });

            tierPremiumBtn?.addEventListener('click', () => {
                currentTier = 'premium';
                updateTierButtons();
                load();
            });

            cancelActiveSubscriptionBtn?.addEventListener('click', () => {
                const runCancel = async () => {
                    cancelActiveSubscriptionBtn.disabled = true;

                    try {
                        const res = await fetch(cancelActiveSubscriptionUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                            },
                        });

                        const json = await res.json().catch(() => ({}));
                        const msg = json.message || (res.ok ? 'Subscription cancelled.' : 'Could not cancel subscription.');
                        showAppModal({ title: res.ok ? 'Subscription Cancelled' : 'Subscription', message: msg, confirmText: 'OK' });

                        if (res.ok) {
                            user.activePlanId = null;
                            user.activePlanTier = null;
                            user.activePlanName = null;
                            user.activePlanEndDate = null;

                            if (activeSubscriptionPanel) {
                                activeSubscriptionPanel.style.display = 'none';
                            }

                            load();
                        }
                    } catch (error) {
                        console.error(error);
                        showAppModal({ title: 'Subscription', message: error.message || 'Could not cancel subscription.', confirmText: 'OK' });
                    } finally {
                        cancelActiveSubscriptionBtn.disabled = false;
                    }
                };

                showAppModal({
                    title: 'Cancel Current Subscription?',
                    message: 'You will be able to subscribe to another plan immediately.',
                    confirmText: 'Yes, Cancel',
                    onConfirm: runCancel,
                });
            });

            premiumForm?.addEventListener('submit', async (e) => {
                e.preventDefault();

                if (!premiumPlanId?.value) {
                    showAppModal({ title: 'Premium', message: 'Choose a premium plan first.', confirmText: 'OK' });
                    return;
                }

                if (!premiumTrainerSelect?.value) {
                    showAppModal({ title: 'Premium', message: 'Please choose a trainer.', confirmText: 'OK' });
                    return;
                }

                const submitBtn = document.getElementById('premium-submit-btn');
                if (submitBtn) submitBtn.disabled = true;

                try {
                    const res = await fetch(subscriptionsStore, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            plan_id: premiumPlanId.value,
                            trainer_id: premiumTrainerSelect.value,
                            member_note: premiumMemberNote?.value || null,
                        })
                    });

                    const json = await res.json().catch(() => ({}));
                    const msg = json.message || (res.ok ? 'Premium request submitted.' : 'Subscription failed');
                    showAppModal({ title: res.ok ? 'Premium Submitted' : 'Subscription', message: msg, confirmText: 'OK' });

                    if (res.ok) {
                        premiumForm.reset();
                        if (premiumPlanId) premiumPlanId.value = '';
                        if (premiumPlanLabel) premiumPlanLabel.value = '';
                        if (premiumSection) premiumSection.style.display = 'none';
                        load();
                    }
                } catch (err) {
                    console.error(err);
                    showAppModal({ title: 'Subscription', message: err.message || 'Subscription failed', confirmText: 'OK' });
                } finally {
                    if (submitBtn) submitBtn.disabled = false;
                }
            });

            // initial load
            updateTierButtons();
            load();
        })();
    </script>
@endpush