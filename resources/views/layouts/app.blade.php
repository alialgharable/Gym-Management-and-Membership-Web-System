<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Gym Management System')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon-nobg.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            color-scheme: dark;
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            background: #070707;
            color: #f8f7ec;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: radial-gradient(circle at top left, rgba(255, 221, 89, 0.08), transparent 30%),
                linear-gradient(180deg, #070707 0%, #111111 100%);
            color: #f8f7ec;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .site-shell {
            max-width: 1180px;
            margin: 0 auto;
            padding: 24px;
        }

        .site-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 18px 24px;
            background: rgba(18, 18, 18, 0.96);
            color: #f8f7ec;
            border-radius: 22px;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.35);
            position: sticky;
            top: 18px;
            z-index: 10;
            backdrop-filter: blur(12px);
        }

        .site-brand {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            color: #ffd54f;
        }

        .site-nav {
            display: flex;
            flex-wrap: nowrap;
            gap: 1.2rem;
            align-items: center;
            justify-content: flex-end;
        }

        .nav-group {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            flex-wrap: wrap;
        }

        .nav-link {
            display: inline-flex;
            align-items: center;
            padding: 0.85rem 1.2rem;
            border-radius: 999px;
            border: 1px solid rgba(255, 209, 37, 0.3);
            transition: all 0.2s ease;
            color: #f8f7ec;
            background: rgba(255, 209, 37, 0.12);
        }

        .nav-link:hover {
            transform: translateY(-1px);
            background: rgba(255, 209, 37, 0.2);
            border-color: rgba(255, 209, 37, 0.5);
        }

        .nav-link.active {
            background: rgba(255, 209, 37, 0.24);
            border-color: rgba(255, 209, 37, 0.6);
            color: #fff7cc;
            box-shadow: 0 0 0 1px rgba(255, 209, 37, 0.14) inset;
        }

        @media (max-width: 1100px) {
            .site-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .site-nav {
                width: 100%;
                justify-content: flex-start;
                flex-wrap: wrap;
                gap: 0.75rem;
            }

            .nav-group {
                width: 100%;
            }
        }

        .page-content {
            margin-top: 24px;
            padding: 0;
        }

        .panel {
            background: rgba(15, 15, 15, 0.82);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.28);
            padding: 32px;
            backdrop-filter: blur(8px);
        }

        .section-title {
            margin: 0 0 18px;
            font-size: clamp(1.5rem, 2vw, 2.15rem);
            line-height: 1.1;
            font-weight: 700;
        }

        .section-subtitle {
            margin: 0 0 20px;
            color: #f3f1d1;
            line-height: 1.7;
            max-width: 780px;
        }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        .card-grid {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        }

        .card {
            background: linear-gradient(180deg, #141414, #101010);
            border: 1px solid #343434;
            border-radius: 20px;
            padding: 22px;
            box-shadow: 0 14px 40px rgba(0, 0, 0, 0.38);
            transition: transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            border-color: rgba(255, 209, 37, 0.22);
            box-shadow: 0 20px 46px rgba(0, 0, 0, 0.42);
        }

        .card h2,
        .card h3 {
            margin: 0 0 12px;
            font-size: 1.1rem;
            color: #ffd54f;
        }

        .card p {
            margin: 0 0 10px;
            color: #d7d2ad;
            line-height: 1.6;
        }

        .field-group {
            display: grid;
            gap: 0.75rem;
            margin-bottom: 1.35rem;
        }

        .field-label {
            font-size: 0.95rem;
            font-weight: 600;
            color: #f8f7ec;
        }

        .field-input,
        .field-select,
        .field-button {
            width: 100%;
            border-radius: 16px;
            border: 1px solid #3f3f3f;
            padding: 0.95rem 1rem;
            font-size: 1rem;
            color: #f8f7ec;
            background: #101010;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .field-input:focus,
        .field-select:focus {
            outline: none;
            border-color: #f7d34a;
            box-shadow: 0 0 0 4px rgba(247, 211, 74, 0.18);
        }

        .field-button {
            cursor: pointer;
            background: #f7d34a;
            color: #111111;
            border: none;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .field-button:hover {
            background: #e5c12c;
        }

        .button-secondary {
            background: #141414;
            color: #f8f7ec;
            border: 1px solid #3f3f3f;
        }

        .button-secondary:hover {
            background: #1f1f1f;
        }

        .grid-stack {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .field-divider {
            height: 1px;
            background: #2e2e2e;
            margin: 26px 0;
            border: none;
        }

        .site-footer {
            margin-top: 48px;
            padding-top: 32px;
            border-top: 1px solid #2b2b2b;
            text-align: center;
            color: #a9a89d;
            font-size: 0.9rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.85rem 1.5rem;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            gap: 0.5rem;
        }

        .btn-primary {
            background: #f7d34a;
            color: #111111;
        }

        .btn-primary:hover {
            background: #e5c12c;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #141414;
            color: #f8f7ec;
            border: 1px solid #3f3f3f;
        }

        .btn-secondary:hover {
            background: #1f1f1f;
        }

        .btn-danger {
            background: #d32f2f;
            color: #fff;
        }

        .btn-danger:hover {
            background: #b71c1c;
        }

        .btn-success {
            background: #2e7d32;
            color: #fff;
        }

        .btn-success:hover {
            background: #1b5e20;
        }

        .breadcrumb {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: #f7d34a;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            border-radius: 18px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.02);
        }

        table thead {
            background: #141414;
        }

        table th {
            padding: 1rem;
            text-align: left;
            font-weight: 700;
            color: #f7d34a;
            border-bottom: 2px solid #2b2b2b;
            vertical-align: middle;
        }

        table td {
            padding: 1rem;
            border-bottom: 1px solid #2b2b2b;
            vertical-align: middle;
        }

        table tbody tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.02);
        }

        table tr:hover {
            background: #0a0a0a;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .actions a,
        .actions button {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }

        .profile-menu {
            position: relative;
            display: flex;
            align-items: center;
        }

        .profile-trigger {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 2px solid rgba(255, 209, 37, 0.35);
            background: transparent;
            padding: 0;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .profile-trigger:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 209, 37, 0.7);
        }

        .profile-avatar {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            min-width: 220px;
            background: #141414;
            border: 1px solid #343434;
            border-radius: 14px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
            padding: 0.5rem;
            display: none;
            z-index: 50;
        }

        .profile-dropdown.show {
            display: block;
        }

        .profile-dropdown-user {
            padding: 0.7rem 0.9rem 0.85rem;
        }

        .profile-dropdown-user strong {
            display: block;
            color: #f8f7ec;
            font-size: 0.95rem;
            margin-bottom: 0.2rem;
        }

        .profile-dropdown-user span {
            display: block;
            color: #bdb89c;
            font-size: 0.82rem;
            letter-spacing: 0.03em;
        }

        .profile-dropdown-link {
            display: block;
            width: 100%;
            padding: 0.7rem 0.9rem;
            border-radius: 10px;
            color: #f8f7ec;
            background: transparent;
            border: none;
            text-align: left;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .profile-dropdown-link:hover {
            background: rgba(255, 209, 37, 0.15);
        }

        .profile-dropdown-divider {
            height: 1px;
            margin: 0.4rem 0.25rem;
            background: #2b2b2b;
        }

        .profile-logout {
            color: #ff6b6b;
        }

        .profile-logout:hover {
            background: rgba(255, 0, 0, 0.15);
        }

        .profile-header-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ffd54a;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
        }

        .notification-bell {
            position: relative;
            font-size: 1.4rem;
            color: #ffffff;
            text-decoration: none;
            margin-left: 15px;
            transition: 0.2s ease;
        }

        .notification-bell:hover {
            color: #ffd700;
            transform: scale(1.1);
        }

        .notification-bell .badge {
            position: absolute;
            top: -6px;
            right: -10px;
            background: #ff5555;
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 50%;
            font-weight: bold;
        }

        .flash-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.68);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            z-index: 9999;
            backdrop-filter: blur(6px);
        }

        .flash-modal.show {
            display: flex;
        }

        .flash-modal-card {
            width: min(100%, 460px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: linear-gradient(180deg, #171717, #101010);
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5);
            padding: 1.6rem;
            animation: modalPop 0.2s ease;
        }

        .flash-modal.success .flash-modal-card {
            border-color: rgba(95, 214, 143, 0.35);
        }

        .flash-modal.error .flash-modal-card {
            border-color: rgba(255, 85, 85, 0.35);
        }

        .flash-modal.warning .flash-modal-card {
            border-color: rgba(255, 215, 0, 0.35);
        }

        .flash-modal-header {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            margin-bottom: 1rem;
        }

        .flash-modal-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .flash-modal.success .flash-modal-icon {
            background: rgba(95, 214, 143, 0.14);
            color: #5fd68f;
        }

        .flash-modal.error .flash-modal-icon {
            background: rgba(255, 85, 85, 0.14);
            color: #ff6b6b;
        }

        .flash-modal.warning .flash-modal-icon {
            background: rgba(255, 215, 0, 0.14);
            color: #ffd700;
        }

        .flash-modal-title {
            margin: 0;
            font-size: 1.2rem;
            color: #f8f7ec;
        }

        .flash-modal-subtitle {
            margin: 0.25rem 0 0;
            color: #bfb99c;
            font-size: 0.92rem;
        }

        .flash-modal-body {
            color: #ddd8bc;
            line-height: 1.75;
            font-size: 0.98rem;
            margin-bottom: 1.2rem;
        }

        .flash-modal-errors {
            margin: 0;
            padding-left: 1.2rem;
            color: #ffd2d2;
        }

        .flash-modal-errors li + li {
            margin-top: 0.45rem;
        }

        .flash-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        @keyframes modalPop {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media (max-width: 760px) {
            .site-shell {
                padding: 16px;
            }

            .site-header {
                padding: 16px;
                border-radius: 18px;
            }

            .nav-link {
                width: 100%;
                justify-content: center;
            }

            .profile-menu {
                width: 100%;
                justify-content: flex-end;
            }

            .panel {
                padding: 20px;
            }

            .flash-modal-card {
                padding: 1.2rem;
                border-radius: 20px;
            }
        }
    </style>
</head>

<body>
    @php
        $flashType = null;
        $flashTitle = null;
        $flashMessage = null;

        if (session('success')) {
            $flashType = 'success';
            $flashTitle = 'Success';
            $flashMessage = session('success');
        } elseif (session('error')) {
            $flashType = 'error';
            $flashTitle = 'Something went wrong';
            $flashMessage = session('error');
        } elseif (session('warning')) {
            $flashType = 'warning';
            $flashTitle = 'Warning';
            $flashMessage = session('warning');
        } elseif ($errors->any()) {
            $flashType = 'error';
            $flashTitle = 'Please fix the following';
        }
    @endphp

    @if($flashType)
        <div id="flashModal" class="flash-modal {{ $flashType }} show">
            <div class="flash-modal-card" role="dialog" aria-modal="true" aria-labelledby="flashModalTitle">
                <div class="flash-modal-header">
                    <div class="flash-modal-icon">
                        @if($flashType === 'success')
                            <i class="fa-solid fa-check"></i>
                        @elseif($flashType === 'warning')
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        @else
                            <i class="fa-solid fa-circle-exclamation"></i>
                        @endif
                    </div>

                    <div>
                        <h2 id="flashModalTitle" class="flash-modal-title">{{ $flashTitle }}</h2>
                        <p class="flash-modal-subtitle">Gym Management System</p>
                    </div>
                </div>

                <div class="flash-modal-body">
                    @if($errors->any())
                        <ul class="flash-modal-errors">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @else
                        {{ $flashMessage }}
                    @endif
                </div>

                <div class="flash-modal-actions">
                    <button type="button" class="btn btn-primary" onclick="closeFlashModal()">Okay</button>
                </div>
            </div>
        </div>
    @endif

    <div class="site-shell">
        <header class="site-header">
            <div class="site-brand">
                <a href="/">Gym Management</a>
            </div>

            <nav class="site-nav" aria-label="Primary navigation">
                <div class="nav-group">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    <a class="nav-link {{ request()->routeIs('plans.*') ? 'active' : '' }}" href="{{ route('plans.index') }}">Membership</a>
                    <a class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}" href="{{ route('classes.index') }}">Classes</a>
                    <a class="nav-link {{ request()->routeIs('trainers.*') ? 'active' : '' }}" href="{{ route('trainers.index') }}">Trainers</a>

                    @if(auth()->check() && !auth()->user()->isAdmin() && !auth()->user()->isMember() && !auth()->user()->isTrainer())
                        <a class="nav-link {{ request()->routeIs('trainer-applications.create') ? 'active' : '' }}" href="{{ route('trainer-applications.create') }}">
                            Become a Trainer
                        </a>
                    @endif

                    @if(auth()->check() && auth()->user()->isAdmin())
                        @php
                            $pendingCount = \App\Models\TrainerApplication::where('status', 'pending')->count();
                        @endphp

                        <a href="{{ route('trainer-applications.index') }}" class="notification-bell" title="Trainer applications">
                            <i class="fa-solid fa-bell"></i>

                            @if($pendingCount > 0)
                                <span class="badge">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    @endif
                </div>

                @auth
                    <div class="profile-menu">
                        <button class="profile-trigger" type="button" onclick="toggleProfileMenu()">
                            <img
                                src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('images/default-avatar.png') }}"
                                alt="Profile"
                                class="profile-avatar"
                                onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';"
                            >
                        </button>

                        <div class="profile-dropdown" id="profileDropdown">
                            @php
                                $user = auth()->user();
                            @endphp

                            <div class="profile-dropdown-user">
                                <strong>{{ $user->name }}</strong>
                                <span>
                                    @if($user->admin)
                                        Admin
                                    @elseif($user->trainer)
                                        Trainer
                                    @elseif($user->member)
                                        Member
                                    @else
                                        User
                                    @endif
                                </span>
                            </div>

                            <div class="profile-dropdown-divider"></div>

                            @if($user->isAdmin() && $user->admin)
                                <a href="{{ route('admins.show', $user->admin->id) }}" class="profile-dropdown-link">Profile</a>
                                <a href="{{ route('admins.edit', $user->admin->id) }}" class="profile-dropdown-link">Edit Profile</a>
                            @elseif($user->trainer)
                                <a href="{{ route('trainers.show', $user->trainer->id) }}" class="profile-dropdown-link">Profile</a>
                                <a href="{{ route('trainers.edit', $user->trainer->id) }}" class="profile-dropdown-link">Edit Profile</a>
                            @elseif($user->member)
                                <a href="{{ route('members.show', $user->member->id) }}" class="profile-dropdown-link">Profile</a>
                                <a href="{{ route('members.edit', $user->member->id) }}" class="profile-dropdown-link">Edit Profile</a>
                            @endif

                            <div class="profile-dropdown-divider"></div>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="profile-dropdown-link profile-logout">Logout</button>
                            </form>
                        </div>
                    </div>
                @endauth

                <div class="nav-group">
                    @guest
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">Login</a>
                        <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">Signup</a>
                    @endguest
                </div>
            </nav>
        </header>

        <main class="page-content">
            <section class="panel">
                @yield('content')
            </section>

            <footer class="site-footer">
                <p>&copy; 2026 Gym Management and Membership System. All rights reserved.</p>
            </footer>
        </main>
    </div>

    <script>
        function toggleProfileMenu() {
            const dropdown = document.getElementById('profileDropdown');
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        }

        function closeFlashModal() {
            const modal = document.getElementById('flashModal');
            if (modal) {
                modal.classList.remove('show');
            }
        }

        document.addEventListener('click', function (event) {
            const menu = document.querySelector('.profile-menu');
            const dropdown = document.getElementById('profileDropdown');

            if (menu && dropdown && !menu.contains(event.target)) {
                dropdown.classList.remove('show');
            }

            const modal = document.getElementById('flashModal');
            if (modal && event.target === modal) {
                closeFlashModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeFlashModal();

                const dropdown = document.getElementById('profileDropdown');
                if (dropdown) {
                    dropdown.classList.remove('show');
                }
            }
        });
    </script>
</body>

</html>