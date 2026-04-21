@extends('layouts.app')

@section('title', 'Forgot Password - GYMRATS')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Recover Password</h1>
            <p class="section-subtitle">Enter your email to receive a reset link.</p>
        </div>
    </div>

    @if (session('status'))
        <div style="margin-bottom: 1rem; padding: 0.85rem 1rem; border-radius: 12px; background: rgba(47, 191, 113, 0.12); border: 1px solid rgba(47, 191, 113, 0.35); color: #9ef0bf;">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="margin-bottom: 1rem; padding: 0.85rem 1rem; border-radius: 12px; background: rgba(239, 71, 111, 0.1); border: 1px solid rgba(239, 71, 111, 0.35); color: #ffb3c4;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if (config('mail.default') === 'log')
        <p style="margin: 0 0 1rem; color: #c8c3a8; line-height: 1.6;">
            Local mode is active. Reset links are written to <strong>storage/logs/laravel.log</strong>.
        </p>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="field-group">
            <label class="field-label" for="email">Email</label>
            <input id="email" type="email" name="email" class="field-input" required value="{{ old('email') }}">
        </div>
        <button type="submit" class="btn btn-primary">Send Reset Link</button>
    </form>
@endsection