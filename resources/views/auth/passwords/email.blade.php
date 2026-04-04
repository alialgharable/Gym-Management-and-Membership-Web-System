@extends('layouts.app')

@section('title', 'Forgot Password - Gym Management')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Recover Password</h1>
            <p class="section-subtitle">Enter your email to receive a reset link.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="field-group">
            <label class="field-label" for="email">Email</label>
            <input id="email" type="email" name="email" class="field-input" required value="{{ old('email') }}">
        </div>
        <button type="submit" class="btn btn-primary">Send Reset Link</button>
    </form>
@endsection