@extends('layouts.app')

@section('title', 'Home')

@section('content')
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

    <div class="page-header">
        <div>
            <h1 class="section-title">Welcome 👋</h1>
            <p class="section-subtitle">
                Discover classes, meet trainers, and take the first step toward your goals
            </p>
        </div>
    </div>

    <div class="card-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); margin-bottom: 2rem;">

        <a href="{{ route('register') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; transition: transform 0.2s ease; height: 100%;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">👥</div>
                <h3>Join as a Member</h3>
                <p style="color: #a9a89d;">Create your account and start booking classes</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">Sign Up →</p>
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

        <a href="{{ route('register') }}" style="text-decoration: none;">
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

    @if(session('showPlansModal'))
    <div id="plansModal" style="
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.75);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    ">

        <div style="
            background: #0f172a;
            padding: 2rem;
            border-radius: 16px;
            width: 90%;
            max-width: 900px;
            position: relative;
        ">

            <!-- ❌ CLOSE BUTTON -->
            <button onclick="closePlansModal()" style="
                position: absolute;
                top: 12px;
                right: 15px;
                background: none;
                border: none;
                font-size: 1.5rem;
                color: white;
                cursor: pointer;
            ">
                ✕
            </button>

            <h2 style="margin-bottom: 1rem;">Choose Your Membership</h2>

            <div style="
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 1rem;
            ">

                @foreach($plans as $plan)
                    <div style="
                        background: #1e293b;
                        padding: 1rem;
                        border-radius: 12px;
                    ">
                        <h3>{{ $plan->name }}</h3>
                        <p style="color:#aaa;">{{ $plan->description }}</p>
                        <p style="font-size:1.5rem;">${{ $plan->price }}</p>

                        <form method="POST" action="{{ route('subscriptions.store') }}">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <button class="btn btn-primary" style="width:100%;">
                                Subscribe
                            </button>
                        </form>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    <script>
        function closePlansModal() {
            document.getElementById('plansModal').style.display = 'none';
        }
    </script>
@endif
@endsection