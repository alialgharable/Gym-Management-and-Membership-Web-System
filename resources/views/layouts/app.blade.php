<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Gym Management System')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
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
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
            justify-content: flex-end;
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

        .page-content {
            margin-top: 24px;
            padding: 0;
        }

        .panel {
            background: #0f0f0f;
            border: 1px solid #2b2b2b;
            border-radius: 24px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
            padding: 32px;
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
            background: #141414;
            border: 1px solid #343434;
            border-radius: 20px;
            padding: 22px;
            box-shadow: 0 14px 40px rgba(0, 0, 0, 0.38);
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

        .alert {
            padding: 20px 24px;
            border-radius: 18px;
            background: #191400;
            border: 1px solid #c59f1a;
            color: #f7d34a;
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

        @media (max-width: 768px) {
            .site-header {
                flex-direction: column;
                align-items: stretch;
            }

            .site-shell {
                padding: 18px;
            }
        }
    </style>
</head>

<body>
    <div class="site-shell">
        <header class="site-header">
            <div class="site-brand">Gym Management</div>
            <nav class="site-nav" aria-label="Primary navigation">
                <a class="nav-link" href="/">Home</a>
                <a class="nav-link" href="/member/dashboard">Member Dashboard</a>
                <a class="nav-link" href="/bookings">My Bookings</a>
                <a class="nav-link" href="/admin/dashboard">Admin Dashboard</a>
            </nav>
        </header>

        <main class="page-content">
            <section class="panel">
                @yield('content')
            </section>
        </main>
    </div>
</body>

</html>