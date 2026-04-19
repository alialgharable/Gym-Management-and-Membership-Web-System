@extends('layouts.app')

@section('title', 'Plan Details')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="section-title" id="plan-name">Plan Details</h1>
            <p class="section-subtitle">Membership plan details</p>
        </div>

        <div class="actions">
            <a href="{{ route('plans.index') }}" class="btn btn-secondary">Back</a>
            @auth
                @if(auth()->user()->isAdmin())
                    <a id="edit-link" class="btn btn-primary" href="#">Edit</a>
                @endif
            @endauth
        </div>
    </div>

    <div id="plan-details" class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
        <div class="card">
            <h3>Plan Information</h3>
            <p><strong>Price:</strong> <span id="plan-price">-</span></p>
            <p><strong>Duration:</strong> <span id="plan-duration">-</span></p>
            <p><strong>Description:</strong> <span id="plan-description">-</span></p>
        </div>

        <div class="card">
            <h3>Subscription Overview</h3>
            <p><strong>Active Subscriptions:</strong> <span id="plan-active">0</span></p>
            <p><strong>Total Subscriptions:</strong> <span id="plan-total">0</span></p>
            <p>
                <strong>Status:</strong>
                <span id="plan-status" style="font-weight:600;"></span>
            </p>
        </div>
    </div>

    <div id="plan-actions">
        @auth
            @if(!auth()->user()->isAdmin() && !auth()->user()->isTrainer())
                <div style="margin-top:1.5rem;">
                    <form id="subscribe-form" action="{{ route('subscriptions.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_id" id="subscribe-plan-id" value="">
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </form>
                </div>
            @endif
        @endauth

        @auth
            @if(auth()->user()->isAdmin())
                <div style="margin-top:1.5rem;">
                    <button id="delete-plan" type="button" class="btn btn-danger">Delete Plan</button>
                </div>
            @endif
        @endauth
    </div>

@endsection

@push('scripts')
    <script>
        (function () {
            const planId = {{ json_encode($planId) }};
            const apiUrl = '/api/plans/' + planId;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const subscriptionsStore = '{{ route('subscriptions.store') }}';

            async function load() {
                try {
                    const res = await fetch(apiUrl, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) throw new Error('Failed to load plan');
                    const json = await res.json();
                    const p = json.data;

                    document.getElementById('plan-name').textContent = p.name;
                    document.getElementById('plan-price').textContent = '$' + Number(p.price).toFixed(2);
                    document.getElementById('plan-duration').textContent = p.duration_label;
                    document.getElementById('plan-description').textContent = p.description || 'No description available.';

                    document.getElementById('plan-active').textContent = p.active_subscriptions || 0;
                    document.getElementById('plan-total').textContent = p.total_subscriptions || 0;

                    const statusEl = document.getElementById('plan-status');
                    if ((p.active_subscriptions || 0) > 0) {
                        statusEl.textContent = 'Popular Plan';
                        statusEl.style.color = '#5fd68f';
                    } else {
                        statusEl.textContent = 'No Active Subscribers';
                        statusEl.style.color = '#a9a89d';
                    }

                    const subscribeInput = document.getElementById('subscribe-plan-id');
                    if (subscribeInput) subscribeInput.value = p.id;

                    // intercept subscribe form to use fetch and avoid page reload
                    const subscribeForm = document.getElementById('subscribe-form');
                    if (subscribeForm && !subscribeForm.__handled) {
                        subscribeForm.addEventListener('submit', async function (e) {
                            e.preventDefault();
                            const btn = subscribeForm.querySelector('button[type="submit"]');
                            if (btn) btn.disabled = true;
                            try {
                                const res = await fetch(subscriptionsStore, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrf,
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ plan_id: p.id })
                                });

                                const json = await res.json().catch(() => ({}));
                                if (!res.ok) {
                                    const msg = json.message || 'Subscribe failed';
                                        if (window.showModal) {
                                            window.showModal({ title: 'Subscription', message: msg, confirmText: 'OK' });
                                        } else {
                                            alert(msg);
                                        }
                                } else {
                                    const msg = json.message || 'Subscribed successfully';
                                    if (window.showModal) {
                                        window.showModal({ title: 'Subscribed', message: msg, confirmText: 'OK' });
                                    } else {
                                        alert(msg);
                                    }
                                }
                            } catch (err) {
                                console.error(err);
                                const msg = err.message || 'Subscribe failed';
                                if (window.showModal) {
                                    window.showModal({ title: 'Subscription', message: msg, confirmText: 'OK' });
                                } else {
                                    alert(msg);
                                }
                            } finally {
                                if (btn) btn.disabled = false;
                            }
                        });
                        subscribeForm.__handled = true;
                    }

                    const editLink = document.getElementById('edit-link');
                    if (editLink) editLink.href = '/plans/' + p.id + '/edit';

                } catch (err) {
                    console.error(err);
                }
            }

            async function destroy() {
                try {
                    const res = await fetch(apiUrl, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                    });
                    if (!res.ok) throw new Error('Delete failed');
                    window.location.href = '/plans';
                } catch (err) {
                    console.error(err);
                    alert('Could not delete plan.');
                }
            }

            document.getElementById('delete-plan')?.addEventListener('click', function () {
                window.showModal({
                    type: 'warning',
                    title: 'Delete Plan?',
                    message: 'This plan will be removed permanently.',
                    confirmText: 'Yes, Delete',
                    onConfirm: destroy
                });
            });

            load();
        })();
    </script>
@endpush