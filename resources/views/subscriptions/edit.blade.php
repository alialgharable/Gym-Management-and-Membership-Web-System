@extends('layouts.app')

@section('title', 'Edit Subscription')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Edit Subscription</h1>
            <p class="section-subtitle">Update subscription details</p>
        </div>
        <a href="{{ route('subscriptions.show', $subscription) }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('subscriptions.update', $subscription) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field-group">
                <label class="field-label">Member </label>
                <input type="text" value="{{ $subscription->member->user->name ?? 'N/A' }}" class="field-input" disabled>
            </div>

            <div class="field-group">
                <label class="field-label">Plan <span style="color: #ff5555;">*</span></label>
                <select name="plan_id" class="field-select" required>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}" @selected($subscription->plan_id == $plan->id)>
                            {{ $plan->name }} - ${{ number_format($plan->price, 2) }} / {{ $plan->duration_months }} months
                        </option>
                    @endforeach
                </select>
                @error('plan_id')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Start Date <span style="color: #ff5555;">*</span></label>
                <input type="date" name="start_date" class="field-input" value="{{ old('start_date', $subscription->start_date->format('Y-m-d')) }}" required>
                @error('start_date')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">End Date <span style="color: #ff5555;">*</span></label>
                <input type="date" name="end_date" class="field-input" value="{{ old('end_date', $subscription->end_date->format('Y-m-d')) }}" required>
                @error('end_date')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Status</label>
                <select name="status" class="field-select">
                    <option value="active" @selected($subscription->status == 'active')>Active</option>
                    <option value="inactive" @selected($subscription->status == 'inactive')>Inactive</option>
                    <option value="expired" @selected($subscription->status == 'expired')>Expired</option>
                </select>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('subscriptions.show', $subscription) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
