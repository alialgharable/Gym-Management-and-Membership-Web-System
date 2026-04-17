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
            align-items: center;
            justify-content: center;
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.08);
            margin-bottom: 1rem;
            line-height: 0;
        }

        .icon-badge svg {
            display: block;
            width: 1.4rem;
            height: 1.4rem;
            fill: #ffd54f;
            transform-box: fill-box;
            transform-origin: center;
            transform: translateY(1px) scale(1.02);
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

        .dumbbell-viewer-shell {
            position: relative;
            margin-bottom: 1.1rem;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: radial-gradient(circle at 30% 20%, rgba(80, 120, 255, 0.18), transparent 40%),
                linear-gradient(160deg, rgba(8, 18, 36, 0.88), rgba(10, 10, 10, 0.94));
            overflow: hidden;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .dumbbell-viewer-canvas {
            width: 100%;
            height: clamp(260px, 32vw, 360px);
        }

        .dumbbell-viewer-caption {
            margin: 0;
            padding: 0.7rem 1rem 0.95rem;
            color: #cfd8ef;
            font-size: 0.85rem;
            letter-spacing: 0.02em;
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
                border-radius: 24px;
            }

            .home-hero-inner {
                gap: 1.35rem;
            }

            .hero-actions {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem;
            }

            .hero-actions .btn {
                width: 100%;
            }

            .hero-highlight {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.75rem;
            }

            .hero-spotlight {
                display: block;
                padding: 0.95rem;
                border-radius: 16px;
            }

            .hero-spotlight strong {
                margin: 0 0 0.2rem;
                font-size: 1.5rem;
            }

            .hero-spotlight span {
                font-size: 0.82rem;
                line-height: 1.35;
            }

            .icon-badge {
                width: 2.1rem;
                height: 2.1rem;
                border-radius: 12px;
                margin-bottom: 0.6rem;
            }

            .icon-badge svg {
                width: 1.18rem;
                height: 1.18rem;
                transform-box: fill-box;
                transform-origin: center;
                transform: translateY(0.75px) scale(1.03);
            }

            .hero-spotlight:nth-child(3) {
                grid-column: 1 / -1;
            }

            .hero-visual-card {
                padding: 1.2rem;
                border-radius: 22px;
            }

            .visual-card-header {
                margin-bottom: 1rem;
            }

            .visual-title {
                font-size: 1.15rem;
            }

            .dumbbell-viewer-shell {
                margin-bottom: 0.75rem;
                border-radius: 16px;
            }

            .dumbbell-viewer-canvas {
                height: 200px;
            }

            .preview-top {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.55rem;
            }

            .preview-status {
                font-size: 0.85rem;
            }

            .preview-tile,
            .preview-small-card {
                border-radius: 14px;
                padding: 0.95rem;
            }

            .preview-tile-large {
                min-height: 0;
            }

            .preview-tile-meta {
                font-size: 0.88rem;
                line-height: 1.5;
                overflow-wrap: anywhere;
            }

            .section-heading {
                align-items: flex-start;
                gap: 0.6rem;
            }

            .section-heading h2 {
                font-size: 1.35rem;
            }

            .section-link {
                font-size: 0.88rem;
            }

            .content-card,
            .quick-action-card {
                padding: 1.15rem;
                border-radius: 18px;
            }

            .content-card-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .meta-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.3rem;
            }

            .preview-grid,
            .preview-footer {
                grid-template-columns: 1fr;
            }

            .hero-copy h1 {
                font-size: 2.2rem;
            }
        }

        @media (max-width: 480px) {
            .home-stack {
                gap: 1.2rem;
            }

            .home-hero {
                padding: 1.25rem 1rem;
                border-radius: 20px;
            }

            .hero-copy h1 {
                font-size: 1.8rem;
            }

            .hero-copy p {
                font-size: 0.94rem;
                line-height: 1.6;
            }

            .preview-chip,
            .visual-badge {
                font-size: 0.78rem;
            }

            .hero-highlight {
                grid-template-columns: 1fr;
            }

            .hero-spotlight:nth-child(3) {
                grid-column: auto;
            }

            .hero-spotlight {
                grid-template-columns: auto 1fr;
                padding: 0.85rem;
            }

            .preview-tile-value,
            .plan-price {
                font-size: 1.65rem;
            }

            .dumbbell-viewer-canvas {
                height: 180px;
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

                        <div class="dumbbell-viewer-shell">
                            <div id="dumbbellViewer" class="dumbbell-viewer-canvas" aria-label="3D dumbbell viewer"></div>
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

    <script type="module">
        import * as THREE from 'three';
        import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
        import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
        import { RoomEnvironment } from 'three/examples/jsm/environments/RoomEnvironment.js';

        let scene;
        let camera;
        let renderer;
        let controls;
        let model;
        let shadowPlane;
        let floorDisk;
        let rafId;
        let startTime = 0;
        let currentRotation = 0;
        let targetRotation = 0;
        let pmremGenerator;
        let modelBaseY = 0;
        const MODEL_VERTICAL_OFFSET = 0.18;

        const container = document.getElementById('dumbbellViewer');
        const MODEL_CANDIDATES = [
            '/models/hex_dumbbell_10kg.glb',
        ];

        function init() {
            if (!container) {
                return;
            }

            scene = new THREE.Scene();

            camera = new THREE.PerspectiveCamera(42, 1, 0.1, 100);
            camera.position.set(2.3, 3.2, 4.0);
            camera.lookAt(0, 0.45, 0);

            renderer = new THREE.WebGLRenderer({
                antialias: true,
                alpha: true,
                powerPreference: 'high-performance',
            });
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.25));
            renderer.setSize(container.clientWidth, container.clientHeight);
            renderer.outputColorSpace = THREE.SRGBColorSpace;
            renderer.toneMapping = THREE.ACESFilmicToneMapping;
            renderer.toneMappingExposure = 1.02;
            renderer.physicallyCorrectLights = true;
            renderer.shadowMap.enabled = true;
            renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            container.appendChild(renderer.domElement);

            pmremGenerator = new THREE.PMREMGenerator(renderer);
            scene.environment = pmremGenerator.fromScene(new RoomEnvironment(), 0.04).texture;
            scene.background = null;

            const hemiLight = new THREE.HemisphereLight(0xbcd6ff, 0x0a0d14, 0.2);
            scene.add(hemiLight);

            const keyLight = new THREE.DirectionalLight(0xfff0de, 1.8);
            keyLight.position.set(4.8, 6.7, 2.7);
            keyLight.castShadow = true;
            keyLight.shadow.mapSize.set(1024, 1024);
            keyLight.shadow.camera.near = 0.3;
            keyLight.shadow.camera.far = 18;
            keyLight.shadow.camera.left = -3.4;
            keyLight.shadow.camera.right = 3.4;
            keyLight.shadow.camera.top = 3.4;
            keyLight.shadow.camera.bottom = -3.4;
            keyLight.shadow.bias = -0.0002;
            keyLight.shadow.normalBias = 0.02;
            scene.add(keyLight);

            const fillLight = new THREE.DirectionalLight(0x8db6ff, 0.5);
            fillLight.position.set(-5.6, 3.2, -3.2);
            scene.add(fillLight);

            const rimLight = new THREE.DirectionalLight(0xb1d4ff, 0.75);
            rimLight.position.set(-1.4, 2.8, 5.8);
            scene.add(rimLight);

            const planeGeometry = new THREE.CircleGeometry(1.25, 48);
            const planeMaterial = new THREE.ShadowMaterial({ opacity: 0.42 });
            shadowPlane = new THREE.Mesh(planeGeometry, planeMaterial);
            shadowPlane.rotation.x = -Math.PI / 2;
            shadowPlane.position.y = -0.8;
            shadowPlane.receiveShadow = true;
            scene.add(shadowPlane);

            const floorGeometry = new THREE.CircleGeometry(1.42, 64);
            const floorMaterial = new THREE.MeshPhysicalMaterial({
                color: 0x111827,
                roughness: 0.62,
                metalness: 0.06,
                clearcoat: 0.24,
                clearcoatRoughness: 0.68,
                transparent: true,
                opacity: 0.55,
            });
            floorDisk = new THREE.Mesh(floorGeometry, floorMaterial);
            floorDisk.rotation.x = -Math.PI / 2;
            floorDisk.position.y = -0.802;
            floorDisk.receiveShadow = true;
            scene.add(floorDisk);

            controls = new OrbitControls(camera, renderer.domElement);
            controls.enableDamping = false;
            controls.enablePan = false;
            controls.minDistance = 3.4;
            controls.maxDistance = 7.5;
            controls.minPolarAngle = Math.PI / 3.2;
            controls.maxPolarAngle = Math.PI / 1.8;
            controls.target.set(0, 0.45, 0);
            controls.update();

            loadModel(0);
            onWindowResize();
            window.addEventListener('resize', onWindowResize);
            animate(0);
        }

        function loadModel(index) {
            const loader = new GLTFLoader();

            if (index >= MODEL_CANDIDATES.length) {
                return;
            }

            loader.load(
                MODEL_CANDIDATES[index],
                (gltf) => {
                    model = gltf.scene;
                    const anisotropy = renderer.capabilities.getMaxAnisotropy();

                    model.traverse((child) => {
                        if (child.isMesh) {
                            child.castShadow = true;
                            child.receiveShadow = true;
                            tuneMaterial(child.material, anisotropy);
                        }
                    });

                    centerAndScaleModel(model);
                    model.rotation.y = -0.45;
                    modelBaseY = model.position.y;
                    scene.add(model);
                },
                undefined,
                () => loadModel(index + 1),
            );
        }

        function tuneMaterial(material, anisotropy) {
            const materials = Array.isArray(material) ? material : [material];

            materials.forEach((mat) => {
                if (!mat) {
                    return;
                }

                if (mat.map) mat.map.anisotropy = anisotropy;
                if (mat.normalMap) mat.normalMap.anisotropy = anisotropy;
                if (mat.roughnessMap) mat.roughnessMap.anisotropy = anisotropy;
                if (mat.metalnessMap) mat.metalnessMap.anisotropy = anisotropy;

                if ('envMapIntensity' in mat) {
                    mat.envMapIntensity = 1.35;
                }

                if ('metalness' in mat) {
                    mat.metalness = THREE.MathUtils.clamp(mat.metalness + 0.1, 0, 1);
                }

                if ('roughness' in mat) {
                    mat.roughness = THREE.MathUtils.clamp(mat.roughness * 0.82, 0.16, 0.95);
                }

                mat.needsUpdate = true;
            });
        }

        function centerAndScaleModel(object3D) {
            const box = new THREE.Box3().setFromObject(object3D);
            const size = box.getSize(new THREE.Vector3());
            const center = box.getCenter(new THREE.Vector3());
            const maxAxis = Math.max(size.x, size.y, size.z) || 1;
            const scale = 2.1 / maxAxis;

            object3D.scale.setScalar(scale);
            object3D.position.sub(center.multiplyScalar(scale));
            object3D.position.y += 0.05;
            object3D.position.y += MODEL_VERTICAL_OFFSET;
        }

        function animate(timestamp) {
            rafId = requestAnimationFrame(animate);

            if (!startTime) {
                startTime = timestamp;
            }

            if (model) {
                const elapsed = (timestamp - startTime) * 0.001;
                targetRotation += 0.005 + Math.sin(elapsed * 0.65) * 0.00045;
                currentRotation = THREE.MathUtils.lerp(currentRotation, targetRotation, 0.12);

                model.rotation.y = currentRotation;
                model.position.y = modelBaseY + Math.sin(elapsed * 0.9) * 0.015;

                if (shadowPlane) {
                    shadowPlane.scale.setScalar(1 + Math.sin(elapsed * 0.9) * 0.012);
                    shadowPlane.material.opacity = 0.43 + Math.cos(elapsed * 0.9) * 0.015;
                }
            }

            renderer.render(scene, camera);
        }

        function onWindowResize() {
            if (!container || !renderer || !camera) {
                return;
            }

            const width = container.clientWidth;
            const height = container.clientHeight;

            if (!width || !height) {
                return;
            }

            camera.aspect = width / height;
            camera.updateProjectionMatrix();
            renderer.setSize(width, height);
        }

        window.addEventListener('beforeunload', () => {
            if (rafId) {
                cancelAnimationFrame(rafId);
            }

            if (controls) {
                controls.dispose();
            }

            if (pmremGenerator) {
                pmremGenerator.dispose();
            }
        });

        init();
    </script>
@endsection