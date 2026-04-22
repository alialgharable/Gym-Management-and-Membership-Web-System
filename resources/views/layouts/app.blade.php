<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'GYMRATS')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script type="importmap">
        {
            "imports": {
                "three": "https://cdn.jsdelivr.net/npm/three@0.161.0/build/three.module.js",
                "three/examples/jsm/controls/OrbitControls.js": "https://cdn.jsdelivr.net/npm/three@0.161.0/examples/jsm/controls/OrbitControls.js",
                "three/examples/jsm/loaders/GLTFLoader.js": "https://cdn.jsdelivr.net/npm/three@0.161.0/examples/jsm/loaders/GLTFLoader.js",
                "three/examples/jsm/environments/RoomEnvironment.js": "https://cdn.jsdelivr.net/npm/three@0.161.0/examples/jsm/environments/RoomEnvironment.js"
            }
        }
    </script>
    <style>
        :root {
            color-scheme: dark;
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            --bg-main: #090d12;
            --bg-elevated: #111922;
            --bg-soft: #162230;
            --text-main: #edf2f7;
            --text-muted: #aeb9c7;
            --line: #273446;
            --accent: #f7d34a;
            --accent-strong: #e5c12c;
            --danger: #ef476f;
            --success: #2fbf71;
            background: var(--bg-main);
            color: var(--text-main);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: radial-gradient(circle at top left, rgba(247, 211, 74, 0.16), transparent 32%),
                linear-gradient(180deg, #090d12 0%, #0e131b 100%);
            color: var(--text-main);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .site-shell {
            max-width: 1180px;
            margin: 0 auto;
            padding: 18px;
        }

        .site-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 14px 18px;
            background: rgba(17, 25, 34, 0.94);
            color: var(--text-main);
            border-radius: 18px;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.35);
            position: sticky;
            top: 12px;
            z-index: 10;
            backdrop-filter: blur(12px);
            border: 1px solid var(--line);
        }

        .site-brand {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            color: var(--accent);
        }

        .site-brand-link {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
        }

        .site-brand-logo {
            width: 34px;
            height: 34px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .site-brand-logo-home {
            width: 34px;
            height: 34px;
        }

        .site-header-main {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
        }

        .nav-toggle {
            display: none;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            border: 1px solid rgba(247, 211, 74, 0.45);
            background: rgba(247, 211, 74, 0.12);
            color: var(--text-main);
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
        }

        .nav-toggle:hover {
            background: rgba(247, 211, 74, 0.22);
            border-color: rgba(247, 211, 74, 0.7);
            transform: translateY(-1px);
        }

        .nav-toggle:focus-visible {
            outline: 2px solid rgba(247, 211, 74, 0.8);
            outline-offset: 2px;
        }

        .site-nav {
            display: flex;
            flex-wrap: nowrap;
            gap: 0.8rem;
            align-items: center;
            justify-content: flex-end;
        }

        .nav-group {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            flex-wrap: wrap;
        }

        .guest-menu {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .guest-menu-trigger {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.2rem 0.15rem;
            border: none;
            border-bottom: 2px solid transparent;
            background: transparent;
            color: var(--text-muted);
            font: inherit;
            font-weight: 600;
            cursor: pointer;
            line-height: 1.2;
            transition: color 0.2s ease, border-color 0.2s ease;
        }

        .guest-menu-trigger:hover,
        .guest-menu-trigger[aria-expanded='true'] {
            color: var(--text-main);
            border-color: var(--accent);
        }

        .guest-menu-trigger i {
            font-size: 0.8rem;
            transition: transform 0.2s ease;
        }

        .guest-menu-trigger[aria-expanded='true'] i {
            transform: rotate(180deg);
        }

        .guest-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            min-width: 240px;
            padding: 0.35rem;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: rgba(17, 25, 34, 0.98);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
            display: none;
            z-index: 60;
        }

        .guest-dropdown.show {
            display: block;
        }

        .guest-dropdown-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 0.55rem 0.75rem;
            border-radius: 9px;
            color: var(--text-main);
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .guest-dropdown-link:hover {
            background: rgba(247, 211, 74, 0.14);
            color: var(--accent);
        }

        .guest-dropdown-link.active {
            background: rgba(247, 211, 74, 0.16);
            color: var(--accent);
        }

        .guest-dropdown-link + .guest-dropdown-link {
            margin-top: 0.15rem;
        }

        .nav-link {
            display: inline-flex;
            align-items: center;
            padding: 0.28rem 0.2rem;
            border-radius: 0;
            border: none;
            transition: color 0.2s ease, border-color 0.2s ease;
            color: var(--text-muted);
            background: transparent;
            font-weight: 600;
            border-bottom: 2px solid transparent;
            line-height: 1.2;
        }

        .nav-link:hover {
            color: var(--accent);
            border-color: rgba(247, 211, 74, 0.6);
        }

        .nav-link.active {
            border-color: var(--accent);
            color: var(--text-main);
            box-shadow: none;
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

            .guest-menu {
                width: 100%;
            }

            .guest-menu-trigger {
                width: 100%;
                justify-content: space-between;
            }

            .guest-dropdown {
                width: 100%;
                left: 0;
                right: auto;
                min-width: 0;
                padding: 0.3rem;
            }

            .guest-dropdown-link {
                padding: 0.5rem 0.7rem;
                font-size: 0.88rem;
            }
        }

        .page-content {
            margin-top: 16px;
            padding: 0;
        }

        .panel {
            background: rgba(17, 25, 34, 0.85);
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.28);
            padding: 22px;
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
            color: var(--text-muted);
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
            background: linear-gradient(180deg, #141d29, #101821);
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 22px;
            box-shadow: 0 14px 40px rgba(0, 0, 0, 0.38);
            transition: transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            border-color: rgba(247, 211, 74, 0.45);
            box-shadow: 0 20px 46px rgba(0, 0, 0, 0.42);
        }

        .card h2,
        .card h3 {
            margin: 0 0 12px;
            font-size: 1.1rem;
            color: var(--accent);
        }

        .card p {
            margin: 0 0 10px;
            color: var(--text-muted);
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
            color: var(--text-main);
        }

        .field-input,
        .field-select,
        .field-button {
            width: 100%;
            border-radius: 16px;
            border: 1px solid var(--line);
            padding: 0.95rem 1rem;
            font-size: 1rem;
            color: var(--text-main);
            background: #0f1722;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .field-input:focus,
        .field-select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(247, 211, 74, 0.18);
        }

        .field-button {
            cursor: pointer;
            background: var(--accent);
            color: #041014;
            border: none;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .field-button:hover {
            background: var(--accent-strong);
        }

        .button-secondary {
            background: var(--bg-soft);
            color: var(--text-main);
            border: 1px solid var(--line);
        }

        .button-secondary:hover {
            background: #1a2a3b;
        }

        .grid-stack {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .field-divider {
            height: 1px;
            background: var(--line);
            margin: 20px 0;
            border: none;
        }

        .site-footer {
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid var(--line);
            color: var(--text-muted);
            font-size: 0.9rem;
            text-align: center;
        }

        .site-footer p {
            margin: 0;
        }

        .footer-inner {
            display: grid;
            gap: 1rem;
            grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr);
            align-items: start;
        }

        .footer-brand {
            display: grid;
            gap: 0.4rem;
        }

        .footer-brand strong {
            color: var(--text-main);
            font-size: 1rem;
        }

        .footer-brand p {
            margin: 0;
            line-height: 1.7;
        }

        .footer-keyinfo {
            display: grid;
            gap: 0.75rem;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }

        .footer-keyinfo-item {
            padding: 0.85rem 1rem;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .footer-keyinfo-item span {
            display: block;
            margin-bottom: 0.2rem;
            color: var(--text-main);
            font-weight: 700;
        }

        .footer-keyinfo-item a {
            color: var(--accent);
        }

        .footer-keyinfo-item a:hover {
            text-decoration: underline;
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
            background: var(--accent);
            color: #041014;
        }

        .btn-primary:hover {
            background: var(--accent-strong);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--bg-soft);
            color: var(--text-main);
            border: 1px solid var(--line);
        }

        .btn-secondary:hover {
            background: #1a2a3b;
        }

        .btn-danger {
            background: var(--danger);
            color: #fff;
        }

        .btn-danger:hover {
            background: #cf355c;
        }

        .btn-success {
            background: var(--success);
            color: #fff;
        }

        .btn-success:hover {
            background: #279f5f;
        }

        .breadcrumb {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: var(--accent);
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
            background: #111a26;
        }

        table th {
            padding: 1rem;
            text-align: left;
            font-weight: 700;
            color: var(--accent);
            border-bottom: 2px solid var(--line);
            vertical-align: middle;
        }

        table td {
            padding: 1rem;
            border-bottom: 1px solid var(--line);
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
            border: 2px solid rgba(247, 211, 74, 0.35);
            background: transparent;
            padding: 0;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .profile-trigger:hover {
            transform: translateY(-2px);
            border-color: rgba(247, 211, 74, 0.7);
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
            background: #111a26;
            border: 1px solid var(--line);
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
            color: var(--text-main);
            font-size: 0.95rem;
            margin-bottom: 0.2rem;
        }

        .profile-dropdown-user span {
            display: block;
            color: var(--text-muted);
            font-size: 0.82rem;
            letter-spacing: 0.03em;
        }

        .profile-dropdown-link {
            display: block;
            width: 100%;
            padding: 0.7rem 0.9rem;
            border-radius: 10px;
            color: var(--text-main);
            background: transparent;
            border: none;
            text-align: left;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .profile-dropdown-link:hover {
            background: rgba(247, 211, 74, 0.15);
        }

        .profile-dropdown-divider {
            height: 1px;
            margin: 0.4rem 0.25rem;
            background: var(--line);
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
            border: 3px solid var(--accent);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
        }

        .notification-bell {
            position: relative;
            font-size: 1.4rem;
            color: var(--text-muted);
            text-decoration: none;
            margin-left: 6px;
            transition: 0.2s ease;
        }

        .notification-bell:hover {
            color: var(--accent);
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
            border-color: rgba(247, 211, 74, 0.35);
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
            background: rgba(247, 211, 74, 0.14);
            color: var(--accent);
        }

        .flash-modal-title {
            margin: 0;
            font-size: 1.2rem;
            color: var(--text-main);
        }

        .flash-modal-subtitle {
            margin: 0.25rem 0 0;
            color: var(--text-muted);
            font-size: 0.92rem;
        }

        .flash-modal-body {
            color: var(--text-main);
            line-height: 1.75;
            font-size: 0.98rem;
            margin-bottom: 1.2rem;
        }

        .flash-modal-errors {
            margin: 0;
            padding-left: 1.2rem;
            color: #ffd2d2;
        }

        .flash-modal-errors li+li {
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
                padding: 12px;
            }

            .site-header {
                padding: 14px;
                border-radius: 16px;
                position: relative;
                top: auto;
                z-index: 2000;
                gap: 0.6rem;
                align-items: stretch;
            }

            .site-brand {
                font-size: 1.05rem;
            }

            .site-brand-logo {
                width: 30px;
                height: 30px;
            }

            .site-brand-logo-home {
                width: 30px;
                height: 30px;
            }

            .site-header-main {
                width: 100%;
            }

            .nav-toggle {
                display: inline-flex;
            }

            .site-nav {
                display: none;
                width: 100%;
                justify-content: stretch;
                gap: 0.4rem;
                margin-top: 0.5rem;
                padding: 0.6rem;
                border: 1px solid var(--line);
                border-radius: 14px;
                background: rgba(16, 24, 33, 0.96);
                flex-direction: column;
                align-items: stretch;
            }

            .site-nav.is-open {
                display: flex;
            }

            .nav-group {
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                gap: 0.15rem;
            }

            .nav-link {
                width: 100%;
                justify-content: flex-start;
                text-align: left;
                padding: 0.45rem 0.1rem;
                font-size: 0.95rem;
            }

            .profile-menu {
                width: 100%;
                justify-content: flex-end;
                margin-top: 0.3rem;
                position: relative;
                z-index: 2100;
            }

            .profile-dropdown {
                z-index: 2200;
            }

            .panel {
                padding: 16px;
                border-radius: 18px;
            }

            .flash-modal-card {
                padding: 1.2rem;
                border-radius: 20px;
            }

            .footer-inner {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .site-shell {
                padding: 10px;
            }

            .page-content {
                margin-top: 14px;
            }

            .site-nav {
                gap: 0.3rem;
            }

            .nav-group {
                gap: 0.1rem;
            }

            .nav-link {
                border-radius: 0;
            }

            .panel {
                padding: 12px;
                border-radius: 14px;
            }
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-box {
            background: #1e1e1e;
            padding: 20px;
            border-radius: 12px;
            width: 320px;
            text-align: center;
        }

        .modal-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

    </style>
</head>

<body>
    {{-- Inline flash messages (replaces modal) --}}
    <div id="flashInline" style="position:fixed; top:16px; right:16px; z-index:9999; max-width:420px;">
        @if (session('success'))
            <div style="margin-bottom:0.75rem; padding:0.85rem 1rem; border-radius:10px; background: rgba(47, 191, 113, 0.12); border:1px solid rgba(47,191,113,0.35); color:#9ef0bf; font-weight:600;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div style="margin-bottom:0.75rem; padding:0.85rem 1rem; border-radius:10px; background: rgba(239,71,111,0.08); border:1px solid rgba(239,71,111,0.25); color:#ffb3c4; font-weight:600;">
                {{ session('error') }}
            </div>
        @endif

        @if (session('warning'))
            <div style="margin-bottom:0.75rem; padding:0.85rem 1rem; border-radius:10px; background: rgba(247,211,74,0.08); border:1px solid rgba(247,211,74,0.25); color:#f7d34a; font-weight:600;">
                {{ session('warning') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="margin-bottom:0.75rem; padding:0.85rem 1rem; border-radius:10px; background: rgba(239,71,111,0.08); border:1px solid rgba(239,71,111,0.25); color:#ffb3c4;">
                <ul style="margin:0; padding-left:1rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="site-shell">
        <header class="site-header">
            <div class="site-header-main">
                <div class="site-brand">
                    <a href="/" class="site-brand-link">
                        <img src="{{ asset('logo.png') }}" alt="GYMRATS logo" class="site-brand-logo {{ request()->routeIs('home') ? 'site-brand-logo-home' : '' }}">
                        <span>GYMRATS</span>
                    </a>
                </div>

                <button id="mobileNavToggle" class="nav-toggle" type="button" aria-label="Toggle navigation menu"
                    aria-expanded="false" aria-controls="siteNav">
                    <i class="fa-solid fa-bars" aria-hidden="true"></i>
                </button>
            </div>

            <nav id="siteNav" class="site-nav" aria-label="Primary navigation">
                <div class="nav-group">
                    @php
                        $homeIsActive = request()->routeIs('home', 'admin.dashboard', 'trainer.dashboard', 'member.dashboard');
                    @endphp

                    <a class="nav-link {{ $homeIsActive ? 'active' : '' }}"
                        href="{{ route('home') }}">Home</a>
                    <a class="nav-link {{ request()->routeIs('plans.*') ? 'active' : '' }}"
                        href="{{ route('plans.index') }}">Membership</a>
                    <a class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}"
                        href="{{ route('classes.index') }}">Classes</a>
                    <a class="nav-link {{ request()->routeIs('trainers.*') ? 'active' : '' }}"
                        href="{{ route('trainers.index') }}">Trainers</a>
                    @php
                        $showProgramsNav = false;

                        if (auth()->check()) {
                            $navUser = auth()->user();

                            if ($navUser->isAdmin() || $navUser->isTrainer()) {
                                $showProgramsNav = true;
                            } elseif ($navUser->isMember() && $navUser->member) {
                                $activeSub = $navUser->member->subscription()
                                    ->with('plan')
                                    ->where('status', 'active')
                                    ->whereDate('end_date', '>=', now()->toDateString())
                                    ->latest('end_date')
                                    ->first();

                                $showProgramsNav = strtolower((string) ($activeSub?->plan?->tier ?? '')) === 'premium';
                            }
                        }
                    @endphp

                    @if($showProgramsNav)
                        <a class="nav-link {{ request()->routeIs('programs.*') ? 'active' : '' }}"
                            href="{{ route('programs.index') }}">Programs</a>
                    @endif
                    <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}"
                        href="{{ route('about') }}">About Us</a>

                    @if(auth()->check() && !auth()->user()->isAdmin() && !auth()->user()->isMember() && !auth()->user()->isTrainer())
                        <a class="nav-link {{ request()->routeIs('trainer-applications.create') ? 'active' : '' }}"
                            href="{{ route('trainer-applications.create') }}">
                            Become a Trainer
                        </a>
                    @endif

                    @if(auth()->check() && auth()->user()->isAdmin())
                        <a class="nav-link {{ request()->routeIs('admin.finance') ? 'active' : '' }}"
                            href="{{ route('admin.finance') }}">Finance</a>

                        @php
                            $pendingCount = \App\Models\TrainerApplication::where('status', 'pending')->count();
                        @endphp

                        <a href="{{ route('trainer-applications.index') }}" class="notification-bell"
                            title="Trainer applications">
                            <i class="fa-solid fa-bell"></i>

                            @if($pendingCount > 0)
                                <span class="badge">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    @endif

                    @if(auth()->check() && auth()->user()->isTrainer() && auth()->user()->trainer)
                        @php
                            $trainerRequestCount = \App\Models\PremiumCoachRequest::where('trainer_id', auth()->user()->trainer->id)
                                ->where('status', 'pending')
                                ->count();
                        @endphp

                        <a href="{{ route('trainer.dashboard') }}#premium-requests" class="notification-bell"
                            title="Premium coach requests">
                            <i class="fa-solid fa-bell"></i>

                            @if($trainerRequestCount > 0)
                                <span class="badge">{{ $trainerRequestCount }}</span>
                            @endif
                        </a>
                    @endif
                </div>

                @auth
                    <div class="profile-menu">
                        <button class="profile-trigger" type="button" onclick="toggleProfileMenu()">
                            <img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('images/default-avatar.png') }}"
                                alt="Profile" class="profile-avatar"
                                onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';">
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
                                <a href="{{ route('admins.edit', $user->admin->id) }}" class="profile-dropdown-link">Edit
                                    Profile</a>
                            @elseif($user->trainer)
                                <a href="{{ route('trainers.show', $user->trainer->id) }}"
                                    class="profile-dropdown-link">Profile</a>
                                <a href="{{ route('trainers.edit', $user->trainer->id) }}" class="profile-dropdown-link">Edit
                                    Profile</a>
                            @elseif($user->member)
                                <a href="{{ route('members.show', $user->member->id) }}"
                                    class="profile-dropdown-link">Profile</a>
                                <a href="{{ route('members.edit', $user->member->id) }}" class="profile-dropdown-link">Edit
                                    Profile</a>
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
                        <div class="guest-menu" id="guestMenu">
                            <button
                                id="guestMenuTrigger"
                                class="guest-menu-trigger"
                                type="button"
                                aria-haspopup="true"
                                aria-expanded="{{ request()->routeIs('login', 'register') ? 'true' : 'false' }}"
                            >
                                <span>Account</span>
                                <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                            </button>

                            <div class="guest-dropdown {{ request()->routeIs('login', 'register') ? 'show' : '' }}" id="guestDropdown">
                                <a class="guest-dropdown-link {{ request()->routeIs('login') ? 'active' : '' }}"
                                    href="{{ route('login') }}">
                                    <span>Login</span>
                                    <i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i>
                                </a>
                                <a class="guest-dropdown-link {{ request()->routeIs('register') ? 'active' : '' }}"
                                    href="{{ route('register') }}">
                                    <span>Signup</span>
                                    <i class="fa-solid fa-user-plus" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    @endguest
                </div>
            </nav>
        </header>

        <main class="page-content">
            <section class="panel">
                @yield('content')
            </section>

            <footer class="site-footer">
                <p>GYMRATS | Beirut, Lebanon | +961 70 000 000 | support@gymrats.com | &copy; 2026 GYMRATS. All rights reserved.</p>
            </footer>
        </main>
    </div>

    <div id="globalModal" class="modal-overlay">
        <div class="modal-box">
            <h3 id="modalTitle">Title</h3>
            <p id="modalMessage">Message</p>

            <div class="modal-actions">
                <button id="modalConfirmBtn" class="btn btn-primary">OK</button>
                <button id="modalCancelBtn" class="btn btn-secondary" style="display:none;">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        window.showModal = function ({
            title = 'Message',
            message = '',
            confirmText = 'OK',
            cancelText = null,
            onConfirm = null
        }) {
            const modal = document.getElementById('globalModal');
            const titleEl = document.getElementById('modalTitle');
            const messageEl = document.getElementById('modalMessage');
            const confirmBtn = document.getElementById('modalConfirmBtn');
            const cancelBtn = document.getElementById('modalCancelBtn');

            titleEl.textContent = title;
            messageEl.textContent = message;
            confirmBtn.textContent = confirmText;

            confirmBtn.onclick = () => {
                hideModal();
                if (onConfirm) onConfirm();
            };

            if (cancelText) {
                cancelBtn.style.display = 'inline-block';
                cancelBtn.textContent = cancelText;
                cancelBtn.onclick = hideModal;
            } else {
                cancelBtn.style.display = 'none';
            }

            modal.classList.add('show');
        };

        window.hideModal = function () {
            document.getElementById('globalModal').classList.remove('show');
        };
    </script>

    <script>
        function toggleProfileMenu() {
            const dropdown = document.getElementById('profileDropdown');
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        }

        function toggleGuestMenu() {
            const trigger = document.getElementById('guestMenuTrigger');
            const dropdown = document.getElementById('guestDropdown');

            if (!trigger || !dropdown) {
                return;
            }

            const isOpen = dropdown.classList.toggle('show');
            trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        }

        function closeGuestMenu() {
            const trigger = document.getElementById('guestMenuTrigger');
            const dropdown = document.getElementById('guestDropdown');

            if (dropdown) {
                dropdown.classList.remove('show');
            }

            if (trigger) {
                trigger.setAttribute('aria-expanded', 'false');
            }
        }

        function closeMobileNav() {
            const nav = document.getElementById('siteNav');
            const toggle = document.getElementById('mobileNavToggle');

            if (nav) {
                nav.classList.remove('is-open');
            }

            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            }
        }

        function toggleMobileNav() {
            const nav = document.getElementById('siteNav');
            const toggle = document.getElementById('mobileNavToggle');

            if (!nav || !toggle) {
                return;
            }

            const isOpen = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
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
            const guestMenu = document.getElementById('guestMenu');
            const guestDropdown = document.getElementById('guestDropdown');
            const guestTrigger = document.getElementById('guestMenuTrigger');
            const nav = document.getElementById('siteNav');
            const navToggle = document.getElementById('mobileNavToggle');

            if (menu && dropdown && !menu.contains(event.target)) {
                dropdown.classList.remove('show');
            }

            if (guestMenu && guestDropdown && guestTrigger && !guestMenu.contains(event.target)) {
                guestDropdown.classList.remove('show');
                guestTrigger.setAttribute('aria-expanded', 'false');
            }

            if (nav && navToggle && nav.classList.contains('is-open')) {
                const isClickInsideNav = nav.contains(event.target);
                const isClickOnToggle = navToggle.contains(event.target);

                if (!isClickInsideNav && !isClickOnToggle) {
                    closeMobileNav();
                }
            }

            const modal = document.getElementById('flashModal');
            if (modal && event.target === modal) {
                closeFlashModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeFlashModal();
                closeMobileNav();
                closeGuestMenu();

                const dropdown = document.getElementById('profileDropdown');
                if (dropdown) {
                    dropdown.classList.remove('show');
                }
            }
        });

        const mobileNavToggle = document.getElementById('mobileNavToggle');
        if (mobileNavToggle) {
            mobileNavToggle.addEventListener('click', toggleMobileNav);
        }

        const guestMenuTrigger = document.getElementById('guestMenuTrigger');
        if (guestMenuTrigger) {
            guestMenuTrigger.addEventListener('click', toggleGuestMenu);
        }

        const navLinks = document.querySelectorAll('#siteNav .nav-link');
        navLinks.forEach((link) => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 760) {
                    closeMobileNav();
                    closeGuestMenu();
                }
            });
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 760) {
                closeMobileNav();
            }

            closeGuestMenu();
        });
    </script>
    @stack('scripts')
</body>

</html>