@extends('layouts.app')

@section('title', 'Finance Overview')

@section('content')

    <style>
        .finance-shell {
            display: grid;
            gap: 1.35rem;
        }

        .finance-hero {
            padding: 1.45rem;
            border-radius: 24px;
            background:
                radial-gradient(circle at top right, rgba(255, 229, 143, 0.14), transparent 30%),
                linear-gradient(145deg, rgba(15, 31, 53, 0.95), rgba(8, 17, 29, 0.96));
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .finance-hero h2 {
            margin: 0 0 0.5rem;
            color: #f8f7ec;
            font-size: 1.35rem;
        }

        .finance-hero p {
            margin: 0;
            color: #c8c3a8;
            line-height: 1.7;
        }

        .finance-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .finance-card {
            padding: 1.15rem;
            border-radius: 18px;
            background: linear-gradient(160deg, rgba(20, 34, 54, 0.94), rgba(10, 18, 30, 0.95));
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 14px 38px rgba(0, 0, 0, 0.2);
        }

        .finance-card h3 {
            margin: 0;
            color: #f8f7ec;
            font-size: 0.97rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .finance-value {
            margin: 0.5rem 0 0.35rem;
            color: #ffd54f;
            font-size: 2rem;
            line-height: 1.08;
            font-weight: 700;
        }

        .finance-meta {
            margin: 0;
            color: #bab59a;
            font-size: 0.9rem;
        }

        .finance-positive {
            color: #5fd68f;
            font-weight: 600;
        }

        .finance-negative {
            color: #ff7a7a;
            font-weight: 600;
        }

        .finance-section {
            display: grid;
            gap: 0.9rem;
        }

        .finance-section-title {
            margin: 0;
            color: #f8f7ec;
            font-size: 1.18rem;
        }

        .finance-subtitle {
            margin: 0;
            color: #bdb89c;
            line-height: 1.6;
        }

        .finance-panel {
            padding: 1rem;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .finance-trend-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 0.85rem;
        }

        .finance-trend-item {
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 0.85rem;
            background: rgba(255, 255, 255, 0.025);
        }

        .finance-trend-label {
            margin: 0 0 0.45rem;
            color: #d2cdb3;
            font-size: 0.86rem;
            font-weight: 600;
        }

        .finance-trend-kpi {
            margin: 0;
            font-size: 1rem;
            color: #f8f7ec;
            line-height: 1.55;
        }

        .finance-table-wrap {
            overflow-x: auto;
        }

        .finance-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        .finance-table th,
        .finance-table td {
            padding: 0.7rem 0.65rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            vertical-align: top;
        }

        .finance-table th {
            color: #f2efdf;
            font-size: 0.87rem;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .finance-table td {
            color: #d6d1b6;
            font-size: 0.92rem;
        }

        .finance-share-bar {
            width: 100%;
            height: 8px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.09);
            overflow: hidden;
            margin-top: 0.35rem;
        }

        .finance-share-fill {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #f7d34a, #ffe68a);
        }
    </style>

    <div class="finance-shell">
        <div class="page-header">
            <div>
                <h1 class="section-title">Finance</h1>
                <p class="section-subtitle">Profitability, portfolio health, payroll pressure, and subscription economics in one board.</p>
            </div>
        </div>

        <div class="finance-hero">
            <h2>Executive Snapshot</h2>
            <p>
                This finance cockpit tracks total subscription sales, recurring revenue potential, payroll outflow, and estimated monthly net performance.
                It also breaks down how each plan and each trainer contributes to portfolio efficiency.
            </p>
        </div>

        <section class="finance-section">
            <h2 class="finance-section-title">Core Financial KPIs</h2>
            <p class="finance-subtitle">Live indicators for revenue velocity, cost pressure, and break-even coverage.</p>

            <div class="finance-grid">
                <div class="finance-card">
                    <h3>Total Subscription Sales</h3>
                    <p class="finance-value">${{ number_format($stats['total_sales'], 2) }}</p>
                    <p class="finance-meta">Gross lifetime sales from all subscriptions</p>
                </div>

                <div class="finance-card">
                    <h3>Estimated MRR</h3>
                    <p class="finance-value">${{ number_format($stats['estimated_mrr'], 2) }}</p>
                    <p class="finance-meta">Normalized monthly recurring revenue from active plans</p>
                </div>

                <div class="finance-card">
                    <h3>Monthly Payroll</h3>
                    <p class="finance-value">${{ number_format($stats['monthly_payroll'], 2) }}</p>
                    <p class="finance-meta">Current trainer salary outflow per month</p>
                </div>

                <div class="finance-card">
                    <h3>Estimated Monthly Net</h3>
                    <p class="finance-value">${{ number_format($stats['estimated_net_monthly_profit'], 2) }}</p>
                    <p class="finance-meta">
                        Net margin:
                        <span class="{{ $stats['margin_percent'] >= 0 ? 'finance-positive' : 'finance-negative' }}">
                            {{ number_format($stats['margin_percent'], 1) }}%
                        </span>
                    </p>
                </div>

                <div class="finance-card">
                    <h3>Sales This Month</h3>
                    <p class="finance-value">${{ number_format($stats['sales_this_month'], 2) }}</p>
                    <p class="finance-meta">{{ number_format($stats['subscriptions_this_month']) }} new subscriptions this month</p>
                </div>

                <div class="finance-card">
                    <h3>Active Subscriptions</h3>
                    <p class="finance-value">{{ number_format($stats['active_subscriptions_count']) }}</p>
                    <p class="finance-meta">
                        Break-even target:
                        <span class="finance-positive">
                            {{ $stats['break_even_subscriptions'] ? number_format($stats['break_even_subscriptions']) : 'N/A' }}
                        </span>
                    </p>
                </div>

                <div class="finance-card">
                    <h3>Average Revenue / Member</h3>
                    <p class="finance-value">${{ number_format($stats['avg_revenue_per_member'], 2) }}</p>
                    <p class="finance-meta">Total sales divided by subscribed members</p>
                </div>

                <div class="finance-card">
                    <h3>Confirmed Bookings</h3>
                    <p class="finance-value">{{ number_format($stats['confirmed_bookings_total']) }}</p>
                    <p class="finance-meta">{{ number_format($stats['bookings_this_month']) }} confirmed bookings this month</p>
                </div>
            </div>
        </section>

        <section class="finance-section">
            <h2 class="finance-section-title">6-Month Finance Trend</h2>
            <p class="finance-subtitle">Compare monthly sales against payroll baseline to monitor operating rhythm.</p>

            <div class="finance-panel">
                <div class="finance-trend-grid">
                    @foreach($financeTrend as $month)
                        <div class="finance-trend-item">
                            <p class="finance-trend-label">{{ $month['label'] }}</p>
                            <p class="finance-trend-kpi">Sales: ${{ number_format($month['sales'], 2) }}</p>
                            <p class="finance-trend-kpi">Payroll: ${{ number_format($month['payroll'], 2) }}</p>
                            <p class="finance-trend-kpi">
                                Profit:
                                <span class="{{ $month['profit'] >= 0 ? 'finance-positive' : 'finance-negative' }}">
                                    ${{ number_format($month['profit'], 2) }}
                                </span>
                            </p>
                            <p class="finance-trend-kpi">New Subs: {{ number_format($month['new_subscriptions']) }}</p>
                            <p class="finance-trend-kpi">Active Subs: {{ number_format($month['active_subscriptions']) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="finance-section">
            <h2 class="finance-section-title">Plan Portfolio</h2>
            <p class="finance-subtitle">Revenue distribution and MRR influence by membership product.</p>

            <div class="finance-panel finance-table-wrap">
                <table class="finance-table">
                    <thead>
                        <tr>
                            <th>Plan</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Total Subs</th>
                            <th>Active Subs</th>
                            <th>Total Revenue</th>
                            <th>Estimated MRR</th>
                            <th>Revenue Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($planPortfolio as $plan)
                            <tr>
                                <td>{{ $plan['name'] }}</td>
                                <td>${{ number_format($plan['price'], 2) }}</td>
                                <td>{{ $plan['duration'] }}</td>
                                <td>{{ number_format($plan['total_subscriptions']) }}</td>
                                <td>{{ number_format($plan['active_subscriptions']) }}</td>
                                <td>${{ number_format($plan['total_revenue'], 2) }}</td>
                                <td>${{ number_format($plan['estimated_mrr'], 2) }}</td>
                                <td>
                                    {{ number_format($plan['revenue_share_percent'], 1) }}%
                                    <div class="finance-share-bar">
                                        <div class="finance-share-fill" style="width: {{ max(0, min(100, $plan['revenue_share_percent'])) }}%;"></div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">No plan finance data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="finance-section">
            <h2 class="finance-section-title">Trainer Payroll Portfolio</h2>
            <p class="finance-subtitle">Payroll load, operational output, and class throughput by trainer.</p>

            <div class="finance-panel finance-table-wrap">
                <table class="finance-table">
                    <thead>
                        <tr>
                            <th>Trainer</th>
                            <th>Specialty</th>
                            <th>Salary</th>
                            <th>Classes</th>
                            <th>Total Bookings</th>
                            <th>Confirmed Bookings</th>
                            <th>Bookings / Class</th>
                            <th>Payroll Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trainerPortfolio as $trainer)
                            <tr>
                                <td>{{ $trainer['name'] }}</td>
                                <td>{{ $trainer['specialty'] }}</td>
                                <td>${{ number_format($trainer['salary'], 2) }}</td>
                                <td>{{ number_format($trainer['classes_count']) }}</td>
                                <td>{{ number_format($trainer['total_bookings']) }}</td>
                                <td>{{ number_format($trainer['confirmed_bookings']) }}</td>
                                <td>{{ number_format($trainer['bookings_per_class'], 2) }}</td>
                                <td>
                                    {{ number_format($trainer['payroll_share_percent'], 1) }}%
                                    <div class="finance-share-bar">
                                        <div class="finance-share-fill" style="width: {{ max(0, min(100, $trainer['payroll_share_percent'])) }}%;"></div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">No trainer payroll data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

@endsection
