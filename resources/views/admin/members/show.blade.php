@extends('layouts.app')

@section('title', 'Member Profile')

@section('content')
    @php
        $user = $member->user;
        $profileImage = $user && $user->profile_picture
            ? asset('storage/' . $user->profile_picture)
            : asset('images/default-avatar.png');

        $activeSub = $member->subscription()
            ->where('status', 'active')
            ->whereDate('end_date', '>=', now()->toDateString())
            ->latest('end_date')
            ->first();
    @endphp

    <div class="card" style="margin-bottom:1.5rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:1.5rem; flex-wrap:wrap;">

            <div style="display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap;">
                <img src="{{ $profileImage }}"
                     style="width:110px; height:110px; border-radius:50%; object-fit:cover; border:3px solid #ffd54a;">

                <div>
                    <h1 class="section-title" style="margin:0;">
                        {{ $user->name ?? 'Member' }}
                    </h1>

                    <p style="color:#aaa; margin:4px 0;">
                        {{ $user->email ?? 'N/A' }}
                    </p>

                    <p style="color:#aaa; font-size:0.9rem;">
                        Member since {{ $member->created_at->format('M d, Y') }}
                    </p>

                    <span style="display:inline-block; margin-top:6px; padding:4px 10px; border-radius:20px; font-size:0.8rem; background:{{ $activeSub ? '#1f3d2b' : '#3d1f1f' }}; color:{{ $activeSub ? '#5fd68f' : '#ff5555' }};">
                        {{ $activeSub ? 'Active Member' : 'No Active Plan' }}
                    </span>
                </div>
            </div>

            <div class="actions" style="display:flex; gap:10px; flex-wrap:wrap;">
                @if(auth()->user()->isMember())
                    <a href="{{ route('member.dashboard') }}" class="btn btn-secondary">Back</a>
                @elseif(auth()->user()->isAdmin())
                    <a href="{{ route('members.index') }}" class="btn btn-secondary">Back</a>
                @endif

                @if(auth()->id() === $member->user_id)
                    <a href="{{ route('members.edit', $member) }}" class="btn btn-primary">
                        Edit Profile
                    </a>
                @endif
            </div>

        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">

        <div class="card">
            <h3>Subscription</h3>

            @if ($activeSub)
                <div style="margin-top:10px;">
                    <p><strong>Plan:</strong> {{ $activeSub->plan->name ?? 'N/A' }}
                        @if(!empty($activeSub->plan?->tier))
                            ({{ ucfirst($activeSub->plan->tier) }})
                        @endif
                    </p>
                    <p><strong>Price:</strong> ${{ $activeSub->plan->price ?? 'N/A' }}</p>
                    <p><strong>Expires:</strong> {{ $activeSub->end_date->format('M d, Y') }}</p>
                </div>
                @if(auth()->id() === $member->user_id)
                    <div style="margin-top:12px;">
                        <button id="cancel-subscription-btn" type="button" class="btn btn-danger">Cancel Subscription</button>
                    </div>
                @endif
            @else
                <p style="color:#ff5555;">No active subscription</p>
            @endif
        </div>

        <div class="card">
            <h3>Account Info</h3>

            <p><strong>ID:</strong> #{{ $member->id }}</p>
            <p><strong>Name:</strong> {{ $user->name ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $user->email ?? 'N/A' }}</p>
        </div>

    </div>

    <div class="card" style="margin-top:1.5rem;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Bookings</h3>
            <span style="color:#aaa;">{{ $member->bookings->count() }} total</span>
        </div>

        @if ($member->bookings->count())
            <ul style="list-style:none; padding:0; margin-top:10px;">
                @foreach ($member->bookings as $booking)
                    <li style="padding:12px 0; border-bottom:1px solid #2b2b2b;">
                        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                            <div>
                                <strong>{{ $booking->gymClass->name ?? 'N/A' }}</strong>
                                <div style="color:#aaa; font-size:0.85rem;">
                                    {{ $booking->created_at->format('M d, Y') }}
                                </div>
                            </div>

                            <span style="color: {{ $booking->status === 'confirmed' ? '#5fd68f' : '#ffd700' }}; font-weight:600;">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <p style="color:#aaa;">No bookings yet</p>
        @endif
    </div>

    @if(auth()->id() === $member->user_id)
        <div style="margin-top:1.5rem;">
            <form action="{{ route('members.destroy', $member) }}" method="POST" class="delete-form">
                @csrf
                @method('DELETE')

                <button type="button" class="btn btn-danger btn-delete">
                    Cancel Membership
                </button>
            </form>
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
                title: 'Cancel Membership?',
                message: 'This will remove your membership permanently.',
                confirmText: 'Yes, Cancel',
                onConfirm: () => form.submit()
            });
        });
    });

    const cancelBtn = document.getElementById('cancel-subscription-btn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            window.showModal({
                title: 'Cancel Subscription?',
                message: 'This will cancel your active subscription. You can delete your membership afterwards.',
                confirmText: 'Yes, Cancel',
                onConfirm: async () => {
                    try {
                        const res = await fetch('{{ route('subscriptions.cancel-active') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                'Accept': 'application/json'
                            }
                        });

                        const json = await res.json().catch(() => ({}));
                        const msg = json.message || (res.ok ? 'Cancelled' : 'Could not cancel');
                        window.showModal({ title: res.ok ? 'Cancelled' : 'Subscription', message: msg, confirmText: 'OK', onConfirm: () => { if (res.ok) { window.location.reload(); } } });
                    } catch (err) {
                        console.error(err);
                        window.showModal({ title: 'Subscription', message: err.message || 'Could not cancel subscription', confirmText: 'OK' });
                    }
                }
            });
        });
    }
</script>
@endpush