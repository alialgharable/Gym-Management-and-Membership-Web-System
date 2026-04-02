@extends('layouts.app')

@section('title', 'Home - Gym Management System')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="section-title">Welcome to Gym Management System</h1>
            <p class="section-subtitle">Your complete solution for managing gym memberships, classes, and trainers</p>
        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); margin-bottom: 2rem;">
        <a href="{{ route('members.index') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; transition: transform 0.2s ease; height: 100%;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">👥</div>
                <h3>Members</h3>
                <p style="color: #a9a89d;">Manage gym members and their profiles</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">View All →</p>
            </div>
        </a>

        <a href="{{ route('classes.index') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; transition: transform 0.2s ease; height: 100%;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">🏋️</div>
                <h3>Classes</h3>
                <p style="color: #a9a89d;">Schedule and manage gym classes</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">View All →</p>
            </div>
        </a>

        <a href="{{ route('trainers.index') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; transition: transform 0.2s ease; height: 100%;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">💪</div>
                <h3>Trainers</h3>
                <p style="color: #a9a89d;">View trainer profiles and specialties</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">View All →</p>
            </div>
        </a>

        <a href="{{ route('bookings.index') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; transition: transform 0.2s ease; height: 100%;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">📅</div>
                <h3>Bookings</h3>
                <p style="color: #a9a89d;">Manage class bookings and schedules</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">View All →</p>
            </div>
        </a>

        <a href="{{ route('plans.index') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; transition: transform 0.2s ease; height: 100%;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">💳</div>
                <h3>Plans</h3>
                <p style="color: #a9a89d;">Manage membership plans and pricing</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">View All →</p>
            </div>
        </a>

        <a href="{{ route('subscriptions.index') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; transition: transform 0.2s ease; height: 100%;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">🔔</div>
                <h3>Subscriptions</h3>
                <p style="color: #a9a89d;">Track active member subscriptions</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">View All →</p>
            </div>
        </a>

        <a href="{{ route('reviews.index') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; transition: transform 0.2s ease; height: 100%;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">⭐</div>
                <h3>Reviews</h3>
                <p style="color: #a9a89d;">Manage trainer reviews and ratings</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">View All →</p>
            </div>
        </a>

        <a href="{{ route('trainer-applications.index') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; transition: transform 0.2s ease; height: 100%;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">📝</div>
                <h3>Applications</h3>
                <p style="color: #a9a89d;">Review trainer applications</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">View All →</p>
            </div>
        </a>

        <a href="{{ route('admin.dashboard') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; transition: transform 0.2s ease; height: 100%;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">📊</div>
                <h3>Dashboard</h3>
                <p style="color: #a9a89d;">View system statistics and overview</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">View All →</p>
            </div>
        </a>
    </div>

    <div class="card" style="background: linear-gradient(135deg, #2b5a7a 0%, #1a3a4a 100%);">
        <h2 style="color: #ffffff; margin-top: 0;">Quick Stats</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div>
                <p style="color: #90caf9; font-size: 0.9rem;">Total Members</p>
                <p style="font-size: 2rem; font-weight: 700; color: #ffffff; margin: 0;">{{ \App\Models\Member::count() }}</p>
            </div>
            <div>
                <p style="color: #a8d5a8; font-size: 0.9rem;">Active Classes</p>
                <p style="font-size: 2rem; font-weight: 700; color: #ffffff; margin: 0;">{{ \App\Models\GymClass::count() }}</p>
            </div>
            <div>
                <p style="color: #ffb74d; font-size: 0.9rem;">Trainers</p>
                <p style="font-size: 2rem; font-weight: 700; color: #ffffff; margin: 0;">{{ \App\Models\Trainer::count() }}</p>
            </div>
            <div>
                <p style="color: #f48fb1; font-size: 0.9rem;">Total Bookings</p>
                <p style="font-size: 2rem; font-weight: 700; color: #ffffff; margin: 0;">{{ \App\Models\Booking::count() }}</p>
            </div>
        </div>
    </div>
@endsection
