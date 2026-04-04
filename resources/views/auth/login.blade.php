@extends('layouts.app')

@section('title', 'Login - Gym Management')

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
            <input id="password" type="password" name="password" class="field-input" required autocomplete="current-password">
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <a href="{{ route('password.request') }}" class="btn btn-secondary" style="padding: 0.7rem 1rem;">Forgot password?</a>
            <button type="submit" class="btn btn-primary">Login</button>
        </div>

        <p>New user? <a href="{{ route('register') }}" style="color: #ffd54f;">Subscribe now</a></p>
    </form>
@endsection