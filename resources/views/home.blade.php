@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <style>
        .home-hero {
            position: relative;
            margin-bottom: 1.75rem;
            padding: 3rem 2.5rem;
            border-radius: 28px;
            overflow: hidden;
            background: radial-gradient(circle at top right, rgba(255, 229, 143, 0.16), transparent 20%),
                radial-gradient(circle at 15% 24%, rgba(67, 138, 255, 0.16), transparent 14%),
                linear-gradient(135deg, #14294f 0%, #091324 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 40px 120px rgba(0, 0, 0, 0.34);
        }

        .home-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 85% 70%, rgba(255, 255, 255, 0.08), transparent 18%);
            pointer-events: none;
        }

        .home-hero-inner {
            position: relative;
            z-index: 1;
            display: grid;
            gap: 2rem;
            grid-template-columns: minmax(0, 1.5fr) minmax(280px, 1fr);
            align-items: center;
        }

        .hero-copy h1 {
            margin: 0 0 1rem;
            font-size: clamp(2.7rem, 5vw, 3.6rem);
            line-height: 1.02;
            letter-spacing: -0.03em;
            color: #f7f3e8;
        }

        .hero-copy p {
            margin: 0 0 1.75rem;
            max-width: 720px;
            color: #dcd8c6;
            font-size: 1.05rem;
            line-height: 1.8;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.75rem;
        }

        .hero-actions .btn {
            min-width: 170px;
        }

        .hero-highlight {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        }

        .hero-spotlight {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.09);
            border-radius: 22px;
            backdrop-filter: blur(10px);
            padding: 1.4rem 1.35rem;
        }

        .hero-spotlight strong {
            display: block;
            font-size: 1.85rem;
            color: #ffd54f;
            margin-bottom: 0.4rem;
        }

        .hero-spotlight span {
            display: block;
            color: #d2cfb4;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .icon-badge {
            display: inline-grid;
            place-items: center;
            width: 2.6rem;
            height: 2.6rem;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.08);
            margin-bottom: 1rem;
        }

        .icon-badge svg {
            width: 1.4rem;
            height: 1.4rem;
            fill: #ffd54f;
        }

        .hero-visual {
            display: grid;
            place-items: center;
        }

        .hero-visual-card {
            width: 100%;
            min-height: 460px;
            padding: 1.8rem;
            border-radius: 32px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.09);
            box-shadow: 0 28px 70px rgba(0, 0, 0, 0.24);
        }

        .visual-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.85rem;
        }

        .visual-label {
            display: inline-block;
            color: #a8c4ff;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.18em;
            font-weight: 700;
        }

        .visual-title {
            margin: 0;
            font-size: 1.35rem;
            color: #f7f3e8;
        }

        .visual-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 0.9rem;
            border-radius: 999px;
            color: #ffd54f;
            background: rgba(255, 213, 84, 0.12);
            font-size: 0.85rem;
            font-weight: 700;
        }

        .dashboard-preview {
            display: grid;
            gap: 1rem;
        }

        .preview-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .preview-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.65rem 0.9rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            color: #dcd8c6;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .preview-status {
            color: #c2c0aa;
            font-size: 0.95rem;
        }

        .preview-main-panel {
            display: grid;
            gap: 1rem;
        }

        .preview-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .preview-tile {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 22px;
            padding: 1.2rem;
            min-height: 120px;
        }

        .preview-tile-large {
            min-height: 200px;
        }

        .preview-tile-title {
            margin: 0 0 0.55rem;
            color: #a8c4ff;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.14em;
            font-weight: 700;
        }

        .preview-tile-value {
            margin: 0;
            color: #ffffff;
            font-size: 2rem;
            line-height: 1.1;
            font-weight: 700;
        }

        .preview-tile-meta {
            margin: 0.85rem 0 0;
            color: #d7d3b7;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .preview-footer {
            display: grid;
            gap: 0.85rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .preview-small-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 1rem;
        }

        .preview-small-card span {
            display: block;
            margin-bottom: 0.45rem;
            color: #bfb99c;
            font-size: 0.92rem;
        }

        .preview-small-card strong {
            display: block;
            margin: 0;
            color: #ffffff;
            font-size: 1.35rem;
            font-weight: 700;
        }

        .hero-features {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 1.35rem 1.4rem;
            transition: transform 0.25s ease, border-color 0.25s ease, background 0.25s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            border-color: rgba(255, 255, 255, 0.14);
            background: rgba(255, 255, 255, 0.06);
        }

        .feature-card h3 {
            margin: 0 0 0.8rem;
            font-size: 1.05rem;
            color: #ffd54f;
        }

        .feature-card p {
            margin: 0;
            color: #d7d3b7;
            line-height: 1.8;
            font-size: 0.98rem;
        }

        .card-grid a .card {
            transition: transform 0.25s ease, border-color 0.25s ease, background 0.25s ease;
        }

        .card-grid a:hover .card {
            transform: translateY(-6px);
            border-color: rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.03);
        }

        .community-tiles {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }

        .community-tile {
            padding: 1.4rem;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .community-tile p {
            margin: 0;
            color: #d8d4b8;
            line-height: 1.7;
        }

        .community-tile strong {
            display: block;
            margin-top: 0.75rem;
            font-size: 2rem;
            color: #ffffff;
            font-weight: 700;
        }
    </style>

    @guest
        <div class="home-hero">
            <div class="home-hero-inner">
                <div class="hero-copy">
                    <h1>Modern gym management with real momentum</h1>
                    <p>View available classes, compare memberships, and find trainers with a clean interface that feels fast, polished, and ready for action.</p>
                    <div class="hero-actions">
                        <a href="{{ route('plans.index') }}" class="btn btn-primary">Explore Plans</a>
                        <a href="{{ route('classes.index') }}" class="btn btn-secondary">Browse Classes</a>
                    </div>

                    <div class="hero-highlight">
                        <div class="hero-spotlight">
                            <span class="icon-badge">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2a2 2 0 0 1 2 2v2h3a1 1 0 0 1 1 1v4h-2V8h-2v3h-4V8H9v4H7V7a1 1 0 0 1 1-1h3V4a2 2 0 0 1 2-2Zm-7 11h2v7h2v-7h2v7h2V13h2v7h2v-7h2v7h2v-7h2v7a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-7Z"/></svg>
                            </span>
                            <strong>{{ \App\Models\GymClass::count() }}</strong>
                            <span>Classes available now</span>
                        </div>
                        <div class="hero-spotlight">
                            <span class="icon-badge">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M9 3H7a1 1 0 0 0-1 1v6H3v2h3v6h2v-6h2v6h2v-6h3v-2h-3V4a1 1 0 0 0-1-1H9Zm2 8H9V5h2v6Zm5 0h-2V5h2v6Z"/></svg>
                            </span>
                            <strong>{{ \App\Models\Trainer::count() }}</strong>
                            <span>Trainers ready to coach</span>
                        </div>
                        <div class="hero-spotlight">
                            <span class="icon-badge">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 3h12a1 1 0 0 1 1 1v2H5V4a1 1 0 0 1 1-1Zm13 4v12a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7h14Zm-9 3H7v6h3V10Zm5 0h-3v6h3v-6Zm2 0h-1v6h1v-6Z"/></svg>
                            </span>
                            <strong>{{ \App\Models\Member::count() }}</strong>
                            <span>Members already active</span>
                        </div>
                    </div>
                </div>

                <div class="hero-visual">
                    <div class="hero-visual-card">
                        <div class="visual-card-header">
                            <div>
                                <span class="visual-label">Live dashboard</span>
                                <h2 class="visual-title">Today’s training flow</h2>
                            </div>
                            <span class="visual-badge">Live</span>
                        </div>

                        <div class="dashboard-preview">
                            <div class="preview-top">
                                <span class="preview-chip">
                                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M4 4h16v3H4V4Zm0 5h10v3H4V9Zm0 5h16v6H4v-6Z" fill="#ffd54f"/></svg>
                                    Schedule
                                </span>
                                <span class="preview-status">85% booked</span>
                            </div>

                            <div class="preview-main-panel">
                                <div class="preview-tile preview-tile-large">
                                    <div class="preview-tile-title">Upcoming sessions</div>
                                    <p class="preview-tile-value">32</p>
                                    <p class="preview-tile-meta">Strong classes, load balance, and trainer availability in one view.</p>
                                </div>

                                <div class="preview-grid">
                                    <div class="preview-tile">
                                        <div class="preview-tile-title">Open seats</div>
                                        <p class="preview-tile-value">18</p>
                                        <p class="preview-tile-meta">Ready for new members to book now.</p>
                                    </div>
                                    <div class="preview-tile">
                                        <div class="preview-tile-title">Active plans</div>
                                        <p class="preview-tile-value">124</p>
                                        <p class="preview-tile-meta">Current member subscriptions and renewals.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="preview-footer">
                                <div class="preview-small-card">
                                    <span>Trainer load</span>
                                    <strong>9/12</strong>
                                </div>
                                <div class="preview-small-card">
                                    <span>Classes today</span>
                                    <strong>7</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
            <h1 class="section-title">A stronger gym experience</h1>
            <p class="section-subtitle">
                Whether you are a member, trainer, or admin, this page puts the most useful actions and insights front and center.
            </p>
        </div>
    </div>

    <div class="card-grid" style="margin-bottom: 2rem;">
        <a href="{{ auth()->check() ? route('plans.index') : route('register') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; height: 100%;">
                <h3>Become a member</h3>
                <p>Create an account, join classes, and manage your subscription effortlessly.</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">Join the community →</p>
            </div>
        </a>

        <a href="{{ route('classes.index') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; height: 100%;">
                <h3>Browse classes</h3>
                <p>See every available class, schedule, and instructor so you can book the right session.</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">View classes →</p>
            </div>
        </a>

        <a href="{{ route('trainers.index') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; height: 100%;">
                <h3>Meet trainers</h3>
                <p>Explore trainer profiles, specialties, and availability to find the best coach.</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">See trainers →</p>
            </div>
        </a>

        <a href="{{ route('plans.index') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; height: 100%;">
                <h3>Latest plans</h3>
                <p>Compare current membership options and choose the plan that fits your goals.</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">Browse plans →</p>
            </div>
        </a>

        <a href="{{ auth()->check() ? route('trainer-applications.create') : route('register') }}" style="text-decoration: none;">
            <div class="card" style="cursor: pointer; height: 100%;">
                <h3>Become a trainer</h3>
                <p>Apply to start coaching members and build your schedule with ease.</p>
                <p style="font-size: 0.9rem; color: #ffd54f; font-weight: 600;">Apply now →</p>
            </div>
        </a>
    </div>

    <div class="card" style="background: linear-gradient(135deg, #2b5a7a 0%, #1a3a4a 100%);">
        <h2 style="color: #ffffff; margin-top: 0;">Community Highlights</h2>
        <div class="community-tiles">
            <div class="community-tile">
                <p>Members joined</p>
                <strong>{{ \App\Models\Member::count() }}</strong>
            </div>
            <div class="community-tile">
                <p>Available classes</p>
                <strong>{{ \App\Models\GymClass::count() }}</strong>
            </div>
            <div class="community-tile">
                <p>Expert trainers</p>
                <strong>{{ \App\Models\Trainer::count() }}</strong>
            </div>
            <div class="community-tile">
                <p>Total bookings</p>
                <strong>{{ \App\Models\Booking::count() }}</strong>
            </div>
        </div>
    </div>
@endsection