@extends('layouts.app')

@section('title', 'Edit Plan')

@section('content')

    @php
        $durationMonths = old('duration_months', max(1, (int) round($plan->duration / 30)));
    @endphp

    <div class="page-header">
        <div>
            <h1 class="section-title">Edit Plan</h1>
            <p class="section-subtitle">Update membership plan</p>
        </div>
        <a href="{{ route('plans.show', $plan) }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card" style="max-width:700px; margin:0 auto;">
        <form action="{{ route('plans.update', $plan) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field-group">
                <label class="field-label">Plan Name</label>
                <input
                    type="text"
                    name="name"
                    class="field-input"
                    value="{{ old('name', $plan->name) }}"
                    required
                >
                @error('name')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Tier</label>
                <select name="tier" class="field-select" required>
                    <option value="basic" @selected(old('tier', $plan->tier ?? 'basic') === 'basic')>Basic</option>
                    <option value="premium" @selected(old('tier', $plan->tier ?? 'basic') === 'premium')>Premium</option>
                </select>
                @error('tier')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Price (USD)</label>
                <input
                    type="number"
                    name="price"
                    class="field-input"
                    value="{{ old('price', $plan->price) }}"
                    step="0.01"
                    min="0"
                    required
                >
                @error('price')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Duration</label>
                <select name="duration_months" class="field-select" required>
                    <option value="1" @selected($durationMonths == 1)>1 Month</option>
                    <option value="3" @selected($durationMonths == 3)>3 Months</option>
                    <option value="6" @selected($durationMonths == 6)>6 Months</option>
                </select>
                @error('duration_months')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Description</label>
                <textarea
                    name="description"
                    class="field-input"
                    rows="4"
                    style="resize:vertical;"
                >{{ old('description', $plan->description) }}</textarea>
                @error('description')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:2rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('plans.show', $plan) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

@endsection