@extends('layouts.app')

@section('title', 'Reset Password - GYMRATS')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Reset Password</h1>
            <p class="section-subtitle">Set a new secure password to log in.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="field-group">
            <label class="field-label" for="email">Email</label>
            <input id="email" type="email" name="email" class="field-input" required value="{{ old('email', $request->email) }}">
        </div>

        <div class="field-group">
            <label class="field-label" for="password">Password</label>
            <input id="password" type="password" name="password" class="field-input" required autocomplete="new-password">
        </div>

        <div class="field-group">
            <label class="field-label" for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="field-input" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary">Reset Password</button>
    </form>
@endsection