@extends('layouts.app')

@section('title', 'Create Subscription')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Create New Subscription</h1>
            <p class="section-subtitle">Add a membership subscription</p>
        </div>
        <a href="{{ route('subscriptions.index') }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card" style="max-width: 600px;">
        <form action="{{ route('subscriptions.store') }}" method="POST">
            @csrf

            <div class="field-group">
                <label class="field-label">Member <span style="color: #ff5555;">*</span></label>
                <select name="member_id" class="field-select" required>
                    <option value="">Select a member...</option>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>
                            {{ $member->user->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
                @error('member_id')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Plan <span style="color: #ff5555;">*</span></label>
                <select name="plan_id" class="field-select" required>
                    <option value="">Select a plan...</option>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}" @selected(old('plan_id') == $plan->id)>
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
                <input type="date" name="start_date" class="field-input" value="{{ old('start_date') }}" required>
                @error('start_date')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">End Date <span style="color: #ff5555;">*</span></label>
                <input type="date" name="end_date" class="field-input" value="{{ old('end_date') }}" required>
                @error('end_date')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Create Subscription</button>
                <a href="{{ route('subscriptions.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
