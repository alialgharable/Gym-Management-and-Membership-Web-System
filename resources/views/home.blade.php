@extends('layouts.app')

@section('title', 'Home')

@section('content')
    @guest
        <div style="
                                                    margin-bottom: 1.75rem;
                                                    padding: 2.25rem;
                                                    border-radius: 20px;
                                                    background: linear-gradient(135deg, rgba(35, 44, 82, 0.94), rgba(14, 23, 45, 0.92));
                                                    border: 1px solid #2a3166;
                                                ">
            <h1 style="font-size: clamp(2rem, 5vw, 2.8rem); margin: 0 0 0.8rem;">
                Start your fitness journey today
            </h1>
            <p style="color: #bebcbc; font-size: 1.05rem; margin-bottom: 1.25rem; max-width: 760px;">
                Join a community of motivated members, explore fitness classes, and connect with professional trainers.
                Create your account to book sessions, track progress, and stay consistent.
            </p>
            <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                <a href="{{ route('login') }}" class="btn btn-primary" style="text-decoration:none;">Login</a>
                <a href="{{ route('register') }}" class="btn btn-secondary" style="text-decoration:none;">Get Started</a>
            </div>
        </div>
    @endguest

    @auth
        @if(!auth()->user()->isMember())
            <div class="card" style="margin-bottom:1.5rem; background:#1e293b;">
                <p style="margin:0;">
                    You’re not subscribed yet.
                    <a href="{{ route('plans.index') }}" style="color:#ffd54f; font-weight:600;">
                        View membership plans →
                    </a>
                </p>
            </div>
        @endif
    @endauth

    <div class="page-header">
        <div>
            <h1 class="section-title">Welcome 👋</h1>
            <p class="section-subtitle">
                Discover classes, meet trainers, and take the first step toward your goals
            </p>
        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); margin-bottom: 2rem;">

        <a href="{{ auth()->check() ? route('plans.index') : route('register') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; transition: transform 0.2s ease; height: 100%;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">👥</div>
                <h3>Join as a Member</h3>
                <p style="color: #a9a89d;">Create your account and start booking classes</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">Join Us →</p>
            </div>
        </a>

        <a href="{{ route('classes.index') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; transition: transform 0.2s ease; height: 100%;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">🏋️</div>
                <h3>Browse Classes</h3>
                <p style="color: #a9a89d;">Explore available fitness classes and schedules</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">View Classes →</p>
            </div>
        </a>

        <a href="{{ route('trainers.index') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; transition: transform 0.2s ease; height: 100%;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">💪</div>
                <h3>Meet Trainers</h3>
                <p style="color: #a9a89d;">Discover experienced trainers and their specialties</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">View Trainers →</p>
            </div>
        </a>

        <a href="{{ route('plans.index') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; transition: transform 0.2s ease; height: 100%;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">💳</div>
                <h3>Membership Plans</h3>
                <p style="color: #a9a89d;">Choose a plan that fits your fitness goals</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">View Plans →</p>
            </div>
        </a>

        <a href="{{ auth()->check() ? route('trainer-applications.create') : route('register') }}"
            style="text-decoration: none;">
            <div class="card" style="cursor: pointer; transition: transform 0.2s ease; height: 100%;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">📝</div>
                <h3>Become a Trainer</h3>
                <p style="color: #a9a89d;">Apply to join and start training members</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">Apply Now →</p>
            </div>
        </a>

    </div>

    <div class="card" style="background: linear-gradient(135deg, #2b5a7a 0%, #1a3a4a 100%);">
        <h2 style="color: #ffffff; margin-top: 0;">Community Highlights</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div>
                <p style="color: #90caf9; font-size: 0.9rem;">Members Joined</p>
                <p style="font-size: 2rem; font-weight: 700; color: #ffffff; margin: 0;">
                    {{ \App\Models\Member::count() }}
                </p>
            </div>
            <div>
                <p style="color: #a8d5a8; font-size: 0.9rem;">Available Classes</p>
                <p style="font-size: 2rem; font-weight: 700; color: #ffffff; margin: 0;">
                    {{ \App\Models\GymClass::count() }}
                </p>
            </div>
            <div>
                <p style="color: #ffb74d; font-size: 0.9rem;">Expert Trainers</p>
                <p style="font-size: 2rem; font-weight: 700; color: #ffffff; margin: 0;">
                    {{ \App\Models\Trainer::count() }}
                </p>
            </div>
            <div>
                <p style="color: #f48fb1; font-size: 0.9rem;">Total Bookings</p>
                <p style="font-size: 2rem; font-weight: 700; color: #ffffff; margin: 0;">
                    {{ \App\Models\Booking::count() }}
                </p>
            </div>
        </div>
    </div>
@endsection