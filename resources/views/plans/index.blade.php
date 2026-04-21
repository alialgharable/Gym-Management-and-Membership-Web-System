@extends('layouts.app')

@section('title', 'Membership Plans')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="section-title">Membership Plans</h1>
            <p class="section-subtitle">Choose the perfect plan for your fitness journey</p>
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
        <div id="plans-list" class="card-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
            <!-- Plans will be rendered here by JS -->
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        (function () {
            const apiBase = '/api/plans';
            const container = document.getElementById('plans-list');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            @php
                $__plans_user = [
                    'isAdmin' => auth()->check() && auth()->user()->isAdmin(),
                    'isTrainer' => auth()->check() && auth()->user()->isTrainer(),
                    'isAuth' => auth()->check(),
                ];
            @endphp

            const user = {!! json_encode($__plans_user) !!};

            const subscriptionsStore = '{{ route('subscriptions.store') }}';

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
                h3.textContent = plan.name;

                const priceWrap = document.createElement('div');
                priceWrap.style.marginBottom = '10px';

                const price = document.createElement('span');
                price.style.fontSize = '2rem';
                price.style.fontWeight = '700';
                price.style.color = '#5fd68f';
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

                if (user.isAuth && !user.isAdmin && !user.isTrainer) {
                    const btn = document.createElement('button');
                    btn.className = 'btn btn-primary';
                    btn.textContent = 'Subscribe';
                    btn.type = 'button';
                    btn.addEventListener('click', async () => {
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
                    alert('Could not delete plan.');
                }
            }

            async function load() {
                container.innerHTML = '';
                try {
                    const res = await fetch(apiBase, { headers: { 'Accept': 'application/json' } });
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

            // initial load
            load();
        })();
    </script>
@endpush