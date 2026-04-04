@extends('layouts.app')

@section('title', 'Register - Gym Management')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Create Your Account</h1>
            <p class="section-subtitle">Sign up as a member or a trainer and start using the gym portal</p>
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="field-group">
            <label class="field-label" for="name">Name</label>
            <input id="name" type="text" name="name" class="field-input" value="{{ old('name') }}" required autofocus>
            @error('name')
                <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="field-group">
            <label class="field-label" for="email">Email</label>
            <input id="email" type="email" name="email" class="field-input" value="{{ old('email') }}" required>
            @error('email')
                <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="field-group">
            <label class="field-label" for="role">I am</label>
            <select name="role" id="role" class="field-select" required onchange="toggleTrainerFields(this.value)">
                <option value="member" selected>Member</option>
                <option value="trainer">Trainer</option>
            </select>
            @error('role')
                <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
            @enderror
        </div>

        <div id="trainer-fields" style="display: none;">
            <div class="field-group">
                <label class="field-label" for="specialty">Trainer Specialty <span style="color: #ff5555;">*</span></label>
                <input id="specialty" type="text" name="specialty" class="field-input" placeholder="e.g., CrossFit, Yoga, Strength Training" value="{{ old('specialty') }}">
                @error('specialty')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label" for="bio">Bio</label>
                <textarea id="bio" name="bio" class="field-input" rows="3" placeholder="Tell us about your experience...">{{ old('bio') }}</textarea>
                @error('bio')
                    <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="field-group">
            <label class="field-label" for="password">Password</label>
            <input id="password" type="password" name="password" class="field-input" required autocomplete="new-password">
            @error('password')
                <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
            @enderror
        </div>

        <div class="field-group">
            <label class="field-label" for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="field-input" required autocomplete="new-password">
            @error('password_confirmation')
                <span style="color: #ff5555; font-size: 0.9rem;">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <script>
        function toggleTrainerFields(role) {
            document.getElementById('trainer-fields').style.display = (role === 'trainer') ? 'block' : 'none';
        }

        window.addEventListener('DOMContentLoaded', function () {
            toggleTrainerFields(document.getElementById('role').value);
        });
    </script>
@endsection