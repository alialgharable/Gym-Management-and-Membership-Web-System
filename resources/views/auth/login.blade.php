@extends('layouts.app')

@section('title', 'Login - GYMRATS')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Login</h1>
            <p class="section-subtitle">Access your member, trainer, or admin dashboard</p>
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field-group">
            <label class="field-label" for="email">Email</label>
            <input id="email" type="email" name="email" class="field-input" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="field-group">
            <label class="field-label" for="password">Password</label>
            <div style="position: relative;">
                <input id="password" type="password" name="password" class="field-input" required autocomplete="current-password" style="padding-right: 3.2rem;">
                <button type="button" onclick="togglePasswordVisibility('password', this)"
                    aria-label="Show password"
                    style="position: absolute; right: 0.9rem; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: #ffd54f; cursor: pointer; font-size: 1rem;">
                    <i class="fa-solid fa-eye-slash"></i>
                </button>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <a href="{{ route('password.request') }}" class="btn btn-secondary" style="padding: 0.7rem 1rem;">Forgot password?</a>
            <button type="submit" class="btn btn-primary">Login</button>
        </div>

        <p>New user? <a href="{{ route('register') }}" style="color: #ffd54f;">Subscribe now</a></p>
    </form>

    @push('scripts')
        <script>
            function togglePasswordVisibility(inputId, button) {
                const input = document.getElementById(inputId);

                if (!input || !button) {
                    return;
                }

                const icon = button.querySelector('i');
                const isHidden = input.type === 'password';

                input.type = isHidden ? 'text' : 'password';

                if (icon) {
                    icon.className = isHidden ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
                }

                button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            }
        </script>
    @endpush
@endsection