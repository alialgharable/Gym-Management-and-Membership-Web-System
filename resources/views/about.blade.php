@extends('layouts.app')

@section('title', 'About Us - GYMRATS')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <style>
        .about-page {
            display: grid;
            gap: 1.2rem;
        }

        .about-page-grid {
            display: grid;
            gap: 1.2rem;
            grid-template-columns: minmax(0, 1.35fr) minmax(280px, 0.85fr);
            align-items: start;
        }

        .about-copy {
            display: grid;
            gap: 1rem;
        }

        .about-copy p {
            margin: 0;
            color: #d8d3bb;
            line-height: 1.75;
        }

        .about-bullets {
            display: grid;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .about-bullet {
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
            padding: 0.95rem 1rem;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.07);
        }

        .about-bullet-icon {
            display: inline-grid;
            place-items: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 12px;
            background: rgba(247, 211, 74, 0.12);
            color: #ffd54f;
            flex-shrink: 0;
        }

        .about-bullet h3 {
            margin: 0 0 0.3rem;
            font-size: 1rem;
            color: #f8f7ec;
        }

        .about-bullet p {
            margin: 0;
            color: #c8c3a8;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .developer-list {
            display: grid;
            gap: 0.65rem;
            margin-top: 1rem;
        }

        .developer-chip {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 1rem;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #f8f7ec;
            font-weight: 600;
        }

        .developer-chip span {
            display: inline-grid;
            place-items: center;
            width: 2rem;
            height: 2rem;
            border-radius: 999px;
            background: rgba(247, 211, 74, 0.14);
            color: #ffd54f;
            font-size: 0.85rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .location-card {
            display: grid;
            gap: 1rem;
        }

        .location-map {
            width: 100%;
            height: 360px;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: #0f1722;
        }

        .location-meta {
            display: grid;
            gap: 0.75rem;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        .location-pill {
            padding: 0.85rem 1rem;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #d8d3bb;
            line-height: 1.6;
        }

        .contact-card {
            display: grid;
            gap: 1rem;
        }

        .contact-grid {
            display: grid;
            gap: 0.75rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .contact-item {
            padding: 0.95rem 1rem;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #d8d3bb;
            line-height: 1.6;
        }

        .contact-item strong {
            display: block;
            margin-bottom: 0.2rem;
            color: #f8f7ec;
        }

        .contact-links {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 0.25rem;
        }

        .contact-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1rem;
            border-radius: 14px;
            background: rgba(247, 211, 74, 0.12);
            border: 1px solid rgba(247, 211, 74, 0.18);
            color: #ffd54f;
            font-weight: 700;
        }

        .contact-link:hover {
            background: rgba(247, 211, 74, 0.18);
        }

        @media (max-width: 1280px) {
            .about-page-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .about-bullet {
                padding: 0.85rem 0.9rem;
            }
        }
    </style>

    <div class="about-page">
        <div class="page-header">
            <div>
                <h1 class="section-title">About Us</h1>
                <p class="section-subtitle">GYMRATS is a complete gym management platform built to keep the entire fitness operation organized in one place.</p>
            </div>
        </div>

        <div class="about-page-grid">
            <div class="content-card">
                <div class="about-copy">
                    <div>
                        <h3>What GYMRATS does</h3>
                        <p>
                            GYMRATS helps gyms manage members, trainers, classes, rooms, bookings, and memberships through a
                            single streamlined system. It is designed to reduce manual work, keep schedules clear, and make
                            the gym experience easier for everyone involved.
                        </p>
                    </div>

                    <p>
                        Members can browse classes and memberships, trainers can manage their schedules and profiles, and
                        administrators can oversee the daily flow of the gym from a modern dashboard.
                    </p>
                </div>

                <div class="about-bullets">
                    <div class="about-bullet">
                        <div class="about-bullet-icon"><i class="fa-solid fa-users"></i></div>
                        <div>
                            <h3>Member Management</h3>
                            <p>Track profiles, subscriptions, and activity in a central member system.</p>
                        </div>
                    </div>

                    <div class="about-bullet">
                        <div class="about-bullet-icon"><i class="fa-solid fa-dumbbell"></i></div>
                        <div>
                            <h3>Class Scheduling</h3>
                            <p>Organize training sessions, room assignments, and trainer availability with clarity.</p>
                        </div>
                    </div>

                    <div class="about-bullet">
                        <div class="about-bullet-icon"><i class="fa-solid fa-credit-card"></i></div>
                        <div>
                            <h3>Membership Plans</h3>
                            <p>Manage flexible plans so members can choose the option that fits their goals.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <h3>Why it matters</h3>
                <p>
                    A gym runs best when everyone can see what is happening, what is available, and where each class belongs.
                    GYMRATS keeps all of that aligned across admins, trainers, and members.
                </p>

                <div class="meta-list" style="margin-top:1rem;">
                    <div class="meta-item">
                        <span>Focus</span>
                        <strong>Fast operations</strong>
                    </div>
                    <div class="meta-item">
                        <span>Coverage</span>
                        <strong>Classes, rooms, trainers, and memberships</strong>
                    </div>
                    <div class="meta-item">
                        <span>Experience</span>
                        <strong>Simple for users, powerful for staff</strong>
                    </div>
                </div>

                <div style="margin-top:1.25rem; padding-top:1rem; border-top:1px solid rgba(255,255,255,0.08);">
                    <h3 style="margin-bottom:0.75rem;">Developers</h3>
                    <p style="margin:0; color:#c8c3a8; line-height:1.7;">
                        Built by the following developers:
                    </p>

                    <div class="developer-list">
                        <div class="developer-chip"><span>1</span>Mahdi Dagher</div>
                        <div class="developer-chip"><span>2</span>Ali Algharable</div>
                        <div class="developer-chip"><span>3</span>Mahmoud Awali</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card location-card">
            <div class="section-heading" style="padding-bottom:0; border-bottom:none; margin-bottom:0;">
                <div>
                    <h2 style="margin:0;">Our Location</h2>
                    <p style="margin:0.4rem 0 0;">Beirut, Lebanon</p>
                </div>
            </div>

            <div id="beirutMap" class="location-map" aria-label="Interactive map showing our location in Beirut, Lebanon"></div>

            <div class="location-meta">
                <div class="location-pill">
                    <strong style="display:block; color:#f8f7ec; margin-bottom:0.2rem;">City</strong>
                    Beirut
                </div>
                <div class="location-pill">
                    <strong style="display:block; color:#f8f7ec; margin-bottom:0.2rem;">Country</strong>
                    Lebanon
                </div>
                <div class="location-pill">
                    <strong style="display:block; color:#f8f7ec; margin-bottom:0.2rem;">Status</strong>
                    Open on the map for visitors
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const mapEl = document.getElementById('beirutMap');

                if (!mapEl || typeof L === 'undefined') {
                    return;
                }

                const beirut = [33.8938, 35.5018];
                const map = L.map('beirutMap', {
                    scrollWheelZoom: false,
                }).setView(beirut, 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                }).addTo(map);

                L.marker(beirut).addTo(map)
                    .bindPopup('<strong>GYMRATS</strong><br>Beirut, Lebanon')
                    .openPopup();
            });
        </script>
    @endpush
@endsection
