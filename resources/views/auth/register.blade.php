@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Create Your Account</h1>
            <p class="section-subtitle">Sign up to start your fitness journey</p>
        </div>
    </div>

    @if ($errors->any())
        <div style="color:#ff5555; margin-bottom:1rem;">
            <ul style="margin:0; padding-left:1.2rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="field-group">
            <label class="field-label" for="name">Name</label>
            <input id="name" type="text" name="name" class="field-input" value="{{ old('name') }}" required autofocus
                autocomplete="name">
            @error('name')
                <span style="color:#ff5555;">{{ $message }}</span>
            @enderror
        </div>

        <div class="field-group">
            <label class="field-label" for="email">Email</label>
            <input id="email" type="email" name="email" class="field-input" value="{{ old('email') }}" required
                autocomplete="email">
            @error('email')
                <span style="color:#ff5555;">{{ $message }}</span>
            @enderror
        </div>

        <div class="field-group">
            <label class="field-label" for="password">Password</label>
            <div style="position: relative;">
                <input id="password" type="password" name="password" class="field-input" required autocomplete="new-password" style="padding-right: 3.2rem;">
                <button type="button" onclick="togglePasswordVisibility(this)"
                    aria-label="Show password"
                    style="position: absolute; right: 0.9rem; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: #ffd54f; cursor: pointer; font-size: 1rem;">
                    <i class="fa-solid fa-eye-slash"></i>
                </button>
            </div>
            @error('password')
                <span style="color:#ff5555;">{{ $message }}</span>
            @enderror
        </div>

        <div class="field-group">
            <label class="field-label" for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="field-input" required
                autocomplete="new-password">
            @error('password_confirmation')
                <span style="color:#ff5555;">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    @push('scripts')
        <script>
            function togglePasswordVisibility(button) {
                const passwordInput = document.getElementById('password');
                const confirmInput = document.getElementById('password_confirmation');

                if (!passwordInput || !confirmInput || !button) {
                    return;
                }

                const icon = button.querySelector('i');
                const isHidden = passwordInput.type === 'password';

                passwordInput.type = isHidden ? 'text' : 'password';
                confirmInput.type = isHidden ? 'text' : 'password';

                if (icon) {
                    icon.className = isHidden ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
                }

                button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            }
        </script>
    @endpush
@endsection