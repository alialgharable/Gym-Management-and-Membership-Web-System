@extends('layouts.app')

@section('title', 'Create Plan')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="section-title">Create New Plan</h1>
            <p class="section-subtitle">Add a new membership plan</p>
        </div>
        <a href="{{ route('plans.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card" style="max-width:700px; margin:0 auto;">
        <form action="{{ route('plans.store') }}" method="POST">
            @csrf

            <div class="field-group">
                <label class="field-label">Plan Name</label>
                <input
                    type="text"
                    name="name"
                    class="field-input"
                    value="{{ old('name') }}"
                    placeholder="e.g. Gold Plan"
                    required
                >
                @error('name')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Price (USD)</label>
                <input
                    type="number"
                    name="price"
                    class="field-input"
                    value="{{ old('price') }}"
                    step="0.01"
                    min="0"
                    placeholder="29.99"
                    required
                >
                @error('price')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Duration</label>
                <select name="duration_months" class="field-select" required>
                    <option value="">Select duration...</option>
                    <option value="1" @selected(old('duration_months') == 1)>1 Month</option>
                    <option value="3" @selected(old('duration_months') == 3)>3 Months</option>
                    <option value="6" @selected(old('duration_months') == 6)>6 Months</option>
                    <option value="12" @selected(old('duration_months') == 12)>12 Months</option>
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
                    placeholder="Plan benefits and details"
                >{{ old('description') }}</textarea>
                @error('description')
                    <span style="color:#ff5555; font-size:0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:2rem;">
                <button type="submit" class="btn btn-primary">Create Plan</button>
                <a href="{{ route('plans.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

@endsection