@extends('layouts.app')

@section('title', 'Home')

@section('content')
    @php
        $featuredPlans = $featuredPlans ?? \App\Models\MembershipPlan::latest()->take(3)->get();
        $featuredClasses = $featuredClasses ?? \App\Models\GymClass::with('trainer.user')
            ->where('schedule', '>=', now())
            ->orderBy('schedule')
            ->take(3)
            ->get();
        $featuredTrainers = $featuredTrainers ?? \App\Models\Trainer::with('user')
            ->latest()
            ->take(3)
            ->get();

        $user = auth()->user();

        $quickActions = [];

        if (!$user) {
            $quickActions = [
                [
                    'title' => 'Join as a Member',
                    'description' => 'Create an account, choose a membership plan, and start booking classes.',
                    'route' => route('register'),
                    'label' => 'Create Account',
                ],
                [
                    'title' => 'Browse Classes',
                    'description' => 'Explore upcoming sessions, schedules, and available spots before you join.',
                    'route' => route('classes.index'),
                    'label' => 'View Classes',
                ],
                [
                    'title' => 'Meet Trainers',
                    'description' => 'Discover expert coaches, specialties, and training styles that match your goals.',
                    'route' => route('trainers.index'),
                    'label' => 'See Trainers',
                ],
            ];
        } elseif ($user->member) {
            $quickActions = [
                [
                    'title' => 'My Profile',
                    'description' => 'Manage your member details, profile picture, and account information.',
                    'route' => route('members.show', $user->member),
                    'label' => 'Open Profile',
                ],
                [
                    'title' => 'Browse Classes',
                    'description' => 'Check upcoming classes and book the sessions that fit your schedule.',
                    'route' => route('classes.index'),
                    'label' => 'View Classes',
                ],
                [
                    'title' => 'Membership Plans',
                    'description' => 'Review available plans and compare your membership options.',
                    'route' => route('plans.index'),
                    'label' => 'View Plans',
                ],
            ];
        } elseif ($user->trainer) {
            $quickActions = [
                [
                    'title' => 'My Trainer Profile',
                    'description' => 'View your public trainer profile, specialty, and coaching details.',
                    'route' => route('trainers.show', $user->trainer),
                    'label' => 'Open Profile',
                ],
                [
                    'title' => 'Manage Classes',
                    'description' => 'Create, update, and manage the classes you are responsible for.',
                    'route' => route('trainer.dashboard'),
                    'label' => 'Open Dashboard',
                ],
                [
                    'title' => 'Edit Profile',
                    'description' => 'Keep your bio, specialty, and profile details up to date.',
                    'route' => route('trainers.edit', $user->trainer),
                    'label' => 'Edit Profile',
                ],
            ];
        } else {
            $quickActions = [
                [
                    'title' => 'Membership Plans',
                    'description' => 'Compare current plans and choose the best option for your fitness goals.',
                    'route' => route('plans.index'),
                    'label' => 'Explore Plans',
                ],
                [
                    'title' => 'Browse Classes',
                    'description' => 'See what classes are available today and in the coming days.',
                    'route' => route('classes.index'),
                    'label' => 'View Classes',
                ],
                [
                    'title' => 'Become a Trainer',
                    'description' => 'Apply to join the coaching team and start training members.',
                    'route' => route('trainer-applications.create'),
                    'label' => 'Apply Now',
                ],
            ];
        }
    @endphp

    <style>
        .home-stack {
            display: grid;
            gap: 2rem;
        }

        .home-hero {
            position: relative;
            margin-bottom: 0.25rem;
            padding: 3rem 2.5rem;
            border-radius: 30px;
            overflow: hidden;
            background:
                radial-gradient(circle at top right, rgba(255, 229, 143, 0.16), transparent 20%),
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
            grid-template-columns: minmax(0, 1.45fr) minmax(300px, 1fr);
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
            transition: transform 0.25s ease, border-color 0.25s ease, background 0.25s ease;
        }

        .hero-spotlight:hover {
            transform: translateY(-4px);
            border-color: rgba(255, 255, 255, 0.16);
            background: rgba(255, 255, 255, 0.06);
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
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 14px;
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
            backdrop-filter: blur(12px);
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
            color: #ffffff;
            font-size: 1.35rem;
            font-weight: 700;
        }

        .dashboard-section {
            display: grid;
            gap: 1.2rem;
        }

        .section-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .section-heading h2 {
            margin: 0;
            font-size: 1.55rem;
            color: #f8f7ec;
        }

        .section-heading p {
            margin: 0.4rem 0 0;
            color: #bdb89c;
            line-height: 1.7;
        }

        .section-link {
            color: #ffd54f;
            font-weight: 700;
            font-size: 0.95rem;
        }

        .quick-actions-grid {
            display: grid;
            gap: 1.2rem;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        }

        .quick-action-card {
            position: relative;
            display: block;
            height: 100%;
            padding: 1.5rem;
            border-radius: 22px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.045), rgba(255, 255, 255, 0.02));
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
        }

        .quick-action-card:hover {
            transform: translateY(-6px);
            border-color: rgba(255, 213, 79, 0.32);
            box-shadow: 0 16px 45px rgba(0, 0, 0, 0.28);
        }

        .quick-action-icon {
            display: inline-grid;
            place-items: center;
            width: 3rem;
            height: 3rem;
            border-radius: 16px;
            margin-bottom: 1rem;
            background: rgba(255, 213, 79, 0.12);
            color: #ffd54f;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .quick-action-card h3 {
            margin: 0 0 0.75rem;
            color: #f8f7ec;
            font-size: 1.08rem;
        }

        .quick-action-card p {
            margin: 0;
            color: #c8c3a8;
            line-height: 1.75;
        }

        .quick-action-label {
            display: inline-block;
            margin-top: 1rem;
            color: #ffd54f;
            font-weight: 700;
        }

        .content-grid-3 {
            display: grid;
            gap: 1.2rem;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }

        .content-card {
            position: relative;
            height: 100%;
            padding: 1.45rem;
            border-radius: 24px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0.02));
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 18px 45px rgba(0, 0, 0, 0.18);
            transition: transform 0.25s ease, border-color 0.25s ease;
        }

        .content-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 255, 255, 0.16);
        }

        .content-card-top {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 0.8rem;
            margin-bottom: 1rem;
        }

        .content-card h3 {
            margin: 0;
            color: #f8f7ec;
            font-size: 1.08rem;
        }

        .content-card p {
            margin: 0.3rem 0 0;
            color: #bbb69c;
            line-height: 1.7;
            font-size: 0.95rem;
        }

        .mini-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.4rem 0.75rem;
            border-radius: 999px;
            background: rgba(255, 213, 79, 0.12);
            color: #ffd54f;
            font-size: 0.78rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .meta-list {
            display: grid;
            gap: 0.65rem;
            margin-top: 1rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            color: #d7d2b6;
            font-size: 0.92rem;
            padding-top: 0.7rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .empty-card {
            padding: 1.6rem;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.035);
            border: 1px dashed rgba(255, 255, 255, 0.16);
            color: #beb89d;
            line-height: 1.8;
        }

        .plan-price {
            font-size: 2rem;
            font-weight: 700;
            color: #ffd54f;
            margin-top: 0.9rem;
        }

        .muted-label {
            color: #9e9984;
            font-size: 0.9rem;
        }

        @media (max-width: 1080px) {
            .home-hero-inner {
                grid-template-columns: 1fr;
            }

            .hero-visual-card {
                min-height: unset;
            }
        }

        @media (max-width: 760px) {
            .home-hero {
                padding: 2rem 1.4rem;
            }

            .preview-grid,
            .preview-footer {
                grid-template-columns: 1fr;
            }

            .hero-copy h1 {
                font-size: 2.2rem;
            }
        }
    </style>

    <div class="home-stack">
        <div class="home-hero">
            <div class="home-hero-inner">
                <div class="hero-copy">
                    <h1>Modern gym management with real momentum</h1>
                    <p>
                        View available classes, compare memberships, and find trainers with a clean interface that feels fast,
                        polished, and ready for action.
                    </p>

                    <div class="hero-actions">
                        <a href="{{ route('plans.index') }}" class="btn btn-primary">Explore Plans</a>
                        <a href="{{ route('classes.index') }}" class="btn btn-secondary">Browse Classes</a>
                    </div>

                    <div class="hero-highlight">
                        <div class="hero-spotlight">
                            <span class="icon-badge">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2a2 2 0 0 1 2 2v2h3a1 1 0 0 1 1 1v4h-2V8h-2v3h-4V8H9v4H7V7a1 1 0 0 1 1-1h3V4a2 2 0 0 1 2-2Zm-7 11h2v7h2v-7h2v7h2V13h2v7h2v-7h2v7h2v-7h2v7a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-7Z"/></svg>
                            </span>
                            <strong>{{ $totalClasses }}</strong>
                            <span>Classes available now</span>
                        </div>

                        <div class="hero-spotlight">
                            <span class="icon-badge">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M9 3H7a1 1 0 0 0-1 1v6H3v2h3v6h2v-6h2v6h2v-6h3v-2h-3V4a1 1 0 0 0-1-1H9Zm2 8H9V5h2v6Zm5 0h-2V5h2v6Z"/></svg>
                            </span>
                            <strong>{{ $totalTrainers }}</strong>
                            <span>Trainers ready to coach</span>
                        </div>

                        <div class="hero-spotlight">
                            <span class="icon-badge">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 3h12a1 1 0 0 1 1 1v2H5V4a1 1 0 0 1 1-1Zm13 4v12a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7h14Zm-9 3H7v6h3V10Zm5 0h-3v6h3v-6Zm2 0h-1v6h1v-6Z"/></svg>
                            </span>
                            <strong>{{ $totalMembers }}</strong>
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
                                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 4h16v3H4V4Zm0 5h10v3H4V9Zm0 5h16v6H4v-6Z" fill="#ffd54f"/>
                                    </svg>
                                    Schedule
                                </span>
                                <span class="preview-status">{{ $bookedPercent }}% booked</span>
                            </div>

                            <div class="preview-main-panel">
                                <div class="preview-tile preview-tile-large">
                                    <div class="preview-tile-title">Upcoming sessions</div>
                                    <p class="preview-tile-value">{{ $upcomingSessions }}</p>
                                    <p class="preview-tile-meta">Strong classes, load balance, and trainer availability in one view.</p>
                                </div>

                                <div class="preview-grid">
                                    <div class="preview-tile">
                                        <div class="preview-tile-title">Open seats</div>
                                        <p class="preview-tile-value">{{ $openSeats }}</p>
                                        <p class="preview-tile-meta">Ready for new members to book now.</p>
                                    </div>

                                    <div class="preview-tile">
                                        <div class="preview-tile-title">Active plans</div>
                                        <p class="preview-tile-value">{{ $activePlans }}</p>
                                        <p class="preview-tile-meta">Current member subscriptions and renewals.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="preview-footer">
                                <div class="preview-small-card">
                                    <span>Trainer load</span>
                                    <strong>{{ $trainersWithClasses }}/{{ $totalTrainers }}</strong>
                                </div>

                                <div class="preview-small-card">
                                    <span>Classes today</span>
                                    <strong>{{ $classesToday }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="dashboard-section">
            <div class="section-heading">
                <div>
                    <h2>Quick actions</h2>
                    <p>Jump straight into the most useful parts of the system.</p>
                </div>
            </div>

            <div class="quick-actions-grid">
                @foreach($quickActions as $index => $action)
                    <a href="{{ $action['route'] }}" class="quick-action-card">
                        <span class="quick-action-icon">{{ $index + 1 }}</span>
                        <h3>{{ $action['title'] }}</h3>
                        <p>{{ $action['description'] }}</p>
                        <span class="quick-action-label">{{ $action['label'] }} →</span>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="dashboard-section">
            <div class="section-heading">
                <div>
                    <h2>Upcoming classes</h2>
                    <p>See the next available sessions and discover what is coming up soon.</p>
                </div>
                <a href="{{ route('classes.index') }}" class="section-link">View all classes →</a>
            </div>

            @if($featuredClasses->isNotEmpty())
                <div class="content-grid-3">
                    @foreach($featuredClasses as $gymClass)
                        @php
    $bookedCount = \App\Models\Booking::where('class_id', $gymClass->id)
        ->where('status', 'confirmed')
        ->count();

    $seatsLeft = max(($gymClass->capacity ?? 0) - $bookedCount, 0);
                        @endphp

                        <a href="{{ route('classes.show', $gymClass) }}" style="text-decoration: none;">
                            <div class="content-card">
                                <div class="content-card-top">
                                    <div>
                                        <h3>{{ $gymClass->name ?? 'Training Class' }}</h3>
                                        <p>{{ $gymClass->trainer->user->name ?? 'Trainer not assigned' }}</p>
                                    </div>
                                    <span class="mini-badge">{{ $seatsLeft }} seats left</span>
                                </div>

                                <div class="meta-list">
                                    <div class="meta-item">
                                        <span>Schedule</span>
                                        <strong>{{ \Carbon\Carbon::parse($gymClass->schedule)->format('M d, Y • h:i A') }}</strong>
                                    </div>
                                    <div class="meta-item">
                                        <span>Capacity</span>
                                        <strong>{{ $gymClass->capacity ?? 0 }}</strong>
                                    </div>
                                    <div class="meta-item">
                                        <span>Status</span>
                                        <strong>{{ $seatsLeft > 0 ? 'Open for booking' : 'Full' }}</strong>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-card">
                    No upcoming classes are available right now. New sessions will appear here as soon as they are scheduled.
                </div>
            @endif
        </section>

        <section class="dashboard-section">
            <div class="section-heading">
                <div>
                    <h2>Featured trainers</h2>
                    <p>Meet a few of the coaches currently available in the gym system.</p>
                </div>
                <a href="{{ route('trainers.index') }}" class="section-link">View all trainers →</a>
            </div>

            @if($featuredTrainers->isNotEmpty())
                <div class="content-grid-3">
                    @foreach($featuredTrainers as $trainer)
                        <a href="{{ route('trainers.show', $trainer) }}" style="text-decoration: none;">
                            <div class="content-card">
                                <div class="content-card-top">
                                    <div>
                                        <h3>{{ $trainer->user->name ?? 'Trainer' }}</h3>
                                        <p>{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $trainer->specialty ?? 'General Training')) }}</p>
                                    </div>
                                    <span class="mini-badge">Coach</span>
                                </div>

                                <p>
                                    {{ \Illuminate\Support\Str::limit($trainer->bio ?? 'Experienced trainer ready to help members reach their fitness goals.', 120) }}
                                </p>

                                <div class="meta-list">
                                    <div class="meta-item">
                                        <span>Specialty</span>
                                        <strong>{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $trainer->specialty ?? 'General Training')) }}</strong>
                                    </div>
                                    <div class="meta-item">
                                        <span>Profile</span>
                                        <strong>Open details</strong>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-card">
                    No trainers are available yet. Once trainers are added, their profiles will appear here automatically.
                </div>
            @endif
        </section>

        <section class="dashboard-section">
            <div class="section-heading">
                <div>
                    <h2>Membership plans</h2>
                    <p>Compare a few current membership options and choose the best fit for your routine.</p>
                </div>
                <a href="{{ route('plans.index') }}" class="section-link">View all plans →</a>
            </div>

            @if($featuredPlans->isNotEmpty())
                <div class="content-grid-3">
                    @foreach($featuredPlans as $plan)
                        <a href="{{ route('plans.show', $plan) }}" style="text-decoration: none;">
                            <div class="content-card">
                                <div class="content-card-top">
                                    <div>
                                        <h3>{{ $plan->name ?? 'Membership Plan' }}</h3>
                                        <p>{{ \Illuminate\Support\Str::limit($plan->description ?? 'Flexible membership option for active members.', 110) }}</p>
                                    </div>
                                    <span class="mini-badge">Plan</span>
                                </div>

                                <div class="plan-price">
                                    ${{ number_format((float) ($plan->price ?? 0), 2) }}
                                </div>

                                <div class="muted-label">
                                    {{ $plan->duration ?? 'Flexible duration' }}
                                </div>

                                <div class="meta-list">
                                    <div class="meta-item">
                                        <span>Plan name</span>
                                        <strong>{{ $plan->name ?? 'Membership Plan' }}</strong>
                                    </div>
                                    <div class="meta-item">
                                        <span>Action</span>
                                        <strong>View details</strong>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-card">
                    No membership plans are available right now. Add plans to show them here automatically.
                </div>
            @endif
        </section>
    </div>
@endsection