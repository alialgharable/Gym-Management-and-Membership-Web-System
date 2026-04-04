@extends('layouts.app')

@section('title', 'Home - Gym Management System')

@section('content')
    <div style="
        margin-bottom: 1.75rem;
        padding: 2.25rem;
        border-radius: 20px;
        background: linear-gradient(135deg, rgba(35, 44, 82, 0.94), rgba(14, 23, 45, 0.92));
        border: 1px solid #2a3166;
    ">
        <h1 style="font-size: clamp(2rem, 5vw, 2.8rem); margin: 0 0 0.8rem;">Level up your gym business</h1>
        <p style="color: #bebcbc; font-size: 1.05rem; margin-bottom: 1.25rem; max-width: 760px;">
            Track memberships, classes, bookings, trainers and subscriptions from a single dashboard.
            Subscribe as member, manage also as trainer or administrator.
        </p>
        <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
            <a href="{{ route('login') }}" class="btn btn-primary" style="text-decoration:none;">Login</a>
            <a href="{{ route('register') }}" class="btn btn-secondary" style="text-decoration:none;">Subscribe Now</a>
        </div>
    </div>

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

        <a href="{{ route('member.dashboard') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; transition: transform 0.2s ease; height: 100%;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">🙋</div>
                <h3>Member Dashboard</h3>
                <p style="color: #a9a89d;">Track subscription, bookings, and member activity</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">Open Member Dashboard →</p>
            </div>
        </a>

        <a href="{{ route('trainer.dashboard') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; transition: transform 0.2s ease; height: 100%;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">🧑‍🏫</div>
                <h3>Trainer Dashboard</h3>
                <p style="color: #a9a89d;">See assigned classes, ratings, and trainer workload</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">Open Trainer Dashboard →</p>
            </div>
        </a>

        <a href="{{ route('admin.dashboard') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; transition: transform 0.2s ease; height: 100%;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">📊</div>
                <h3>Admin Dashboard</h3>
                <p style="color: #a9a89d;">Monitor system-wide statistics and operations</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">Open Admin Dashboard →</p>
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
