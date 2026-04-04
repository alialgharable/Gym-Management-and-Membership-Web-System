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
            <input id="name" type="text" name="name" class="field-input" value="{{ old('name') }}" required autofocus>
            @error('name') <span style="color:#ff5555;">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label class="field-label" for="email">Email</label>
            <input id="email" type="email" name="email" class="field-input" value="{{ old('email') }}" required>
            @error('email') <span style="color:#ff5555;">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label class="field-label" for="password">Password</label>
            <input id="password" type="password" name="password" class="field-input" required>
            @error('password') <span style="color:#ff5555;">{{ $message }}</span> @enderror
        </div>

        <div class="field-group">
            <label class="field-label" for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="field-input" required>
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>
@endsection