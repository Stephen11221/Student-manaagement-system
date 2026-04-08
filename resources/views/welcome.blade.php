<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'School Portal') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        <style>
            :root {
                color-scheme: dark;
                --bg: #020617;
                --panel: rgba(15, 23, 42, 0.78);
                --panel-border: rgba(148, 163, 184, 0.18);
                --text: #e2e8f0;
                --muted: #94a3b8;
                --heading: #f8fafc;
                --primary: #22d3ee;
                --primary-strong: #06b6d4;
                --success: #34d399;
                --danger: #fb7185;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                font-family: "Instrument Sans", sans-serif;
                color: var(--text);
                background:
                    radial-gradient(circle at top left, rgba(34, 211, 238, 0.22), transparent 28%),
                    radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.18), transparent 30%),
                    linear-gradient(135deg, #020617 0%, #0f172a 54%, #111827 100%);
            }

            .shell {
                width: min(1180px, calc(100% - 32px));
                margin: 0 auto;
                min-height: 100vh;
                display: grid;
                align-items: center;
                padding: 48px 0;
            }

            .layout {
                display: grid;
                gap: 32px;
                grid-template-columns: minmax(0, 1.15fr) minmax(320px, 0.85fr);
                align-items: center;
            }

            .eyebrow {
                display: inline-flex;
                align-items: center;
                padding: 8px 14px;
                border-radius: 999px;
                border: 1px solid rgba(34, 211, 238, 0.24);
                background: rgba(34, 211, 238, 0.12);
                color: #cffafe;
                font-size: 0.92rem;
                font-weight: 600;
            }

            h1,
            h2,
            p {
                margin: 0;
            }

            .hero h1 {
                margin-top: 20px;
                font-size: clamp(2.7rem, 5vw, 4.7rem);
                line-height: 1.02;
                color: var(--heading);
            }

            .hero .lead {
                margin-top: 22px;
                max-width: 650px;
                font-size: 1.08rem;
                line-height: 1.8;
                color: var(--muted);
            }

            .feature-grid {
                margin-top: 34px;
                display: grid;
                gap: 16px;
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .feature-card,
            .panel {
                border: 1px solid var(--panel-border);
                background: var(--panel);
                backdrop-filter: blur(18px);
                box-shadow: 0 24px 80px rgba(2, 6, 23, 0.32);
            }

            .feature-card {
                border-radius: 24px;
                padding: 22px;
            }

            .feature-card h3 {
                margin-bottom: 10px;
                color: var(--heading);
                font-size: 1rem;
            }

            .feature-card p {
                color: var(--muted);
                line-height: 1.6;
                font-size: 0.95rem;
            }

            .panel {
                border-radius: 32px;
                padding: 34px;
            }

            .panel-tag {
                font-size: 0.82rem;
                font-weight: 700;
                letter-spacing: 0.22em;
                text-transform: uppercase;
                color: #bae6fd;
            }

            .panel h2 {
                margin-top: 12px;
                font-size: 2rem;
                color: var(--heading);
            }

            .panel-copy {
                margin-top: 12px;
                color: var(--muted);
                line-height: 1.7;
            }

            .alert {
                margin-top: 22px;
                padding: 14px 16px;
                border-radius: 18px;
                border: 1px solid;
                font-size: 0.95rem;
            }

            .alert.success {
                color: #d1fae5;
                background: rgba(16, 185, 129, 0.12);
                border-color: rgba(52, 211, 153, 0.28);
            }

            .alert.error {
                color: #ffe4e6;
                background: rgba(244, 63, 94, 0.12);
                border-color: rgba(251, 113, 133, 0.28);
            }

            form {
                margin-top: 28px;
            }

            .field + .field,
            .actions,
            .primary-button,
            .button-row {
                margin-top: 18px;
            }

            label {
                display: block;
                margin-bottom: 8px;
                font-size: 0.94rem;
                font-weight: 600;
                color: #dbeafe;
            }

            input[type="email"],
            input[type="password"] {
                width: 100%;
                border: 1px solid rgba(148, 163, 184, 0.2);
                border-radius: 18px;
                background: rgba(2, 6, 23, 0.56);
                color: var(--heading);
                padding: 14px 16px;
                font: inherit;
                outline: none;
                transition: border-color 0.2s ease, transform 0.2s ease;
            }

            input[type="email"]:focus,
            input[type="password"]:focus {
                border-color: rgba(34, 211, 238, 0.65);
                transform: translateY(-1px);
            }

            .actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                color: var(--muted);
                font-size: 0.9rem;
            }

            .remember {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                margin: 0;
            }

            .remember input {
                accent-color: var(--primary);
            }

            button,
            .button-link {
                border: 0;
                cursor: pointer;
                font: inherit;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 18px;
                padding: 14px 18px;
                font-weight: 700;
                transition: transform 0.2s ease, opacity 0.2s ease, background 0.2s ease;
            }

            button:hover,
            .button-link:hover {
                transform: translateY(-1px);
            }

            .primary-button {
                width: 100%;
                background: linear-gradient(135deg, var(--primary), var(--primary-strong));
                color: #082f49;
            }

            .button-row {
                display: flex;
                gap: 14px;
                flex-wrap: wrap;
            }

            .button-link.secondary,
            .secondary-button {
                background: rgba(255, 255, 255, 0.04);
                color: var(--heading);
                border: 1px solid rgba(148, 163, 184, 0.18);
            }

            .secondary-button {
                min-width: 150px;
            }

            .demo {
                margin-top: 18px;
                color: var(--muted);
                font-size: 0.92rem;
            }

            .demo strong {
                color: var(--heading);
            }

            @media (max-width: 980px) {
                .layout {
                    grid-template-columns: 1fr;
                }

                .feature-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 640px) {
                .shell {
                    width: min(100% - 24px, 1180px);
                    padding: 28px 0;
                }

                .panel,
                .feature-card {
                    padding: 24px;
                }

                .actions,
                .button-row {
                    flex-direction: column;
                    align-items: stretch;
                }
            }
        </style>
    </head>
    <body>
        <main class="shell">
            <div class="layout">
                <section class="hero">
                    <span class="eyebrow">Smart access for students, teachers, and staff</span>
                    <h1>Welcome to the School Portal</h1>
                    <p class="lead">
                        Manage learning, attendance, communication, and school records from one secure place. Sign in to continue to your personalized dashboard.
                    </p>

                    <div class="feature-grid">
                        <article class="feature-card">
                            <h3>Attendance</h3>
                            <p>Track daily presence and class participation in real time.</p>
                        </article>
                        <article class="feature-card">
                            <h3>Academics</h3>
                            <p>View grades, assignments, and academic progress in one place.</p>
                        </article>
                        <article class="feature-card">
                            <h3>Communication</h3>
                            <p>Stay updated with notices, reminders, and important school news.</p>
                        </article>
                    </div>
                </section>

                <section class="panel">
                    @auth
                        <p class="panel-tag" style="color: #86efac;">Signed in</p>
                        <h2>Welcome back, {{ auth()->user()->name }}</h2>
                        <p class="panel-copy">
                            Your portal is ready. Continue to your dashboard to view classes, updates, and school activity.
                        </p>

                        <div class="button-row">
                            <a href="{{ route('dashboard') }}" class="button-link" style="background: linear-gradient(135deg, #34d399, #10b981); color: #052e16;">
                                Open Dashboard
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="secondary-button">Log Out</button>
                            </form>
                        </div>
                    @else
                        <p class="panel-tag">Portal login</p>
                        <h2>Sign in to continue</h2>
                        <p class="panel-copy">
                            Use your school email and password to access your account.
                        </p>

                        @if (session('status'))
                            <div class="alert success">{{ session('status') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert error">{{ $errors->first() }}</div>
                        @endif

                        <form method="POST" action="{{ route('login.attempt') }}">
                            @csrf

                            <div class="field">
                                <label for="email">Email address</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    placeholder="student@schoolportal.com"
                                    required
                                    autofocus
                                >
                            </div>

                            <div class="field">
                                <label for="password">Password</label>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    placeholder="Enter your password"
                                    required
                                >
                            </div>

                            <div class="actions">
                                <label class="remember">
                                    <input type="checkbox" name="remember">
                                    <span>Remember me</span>
                                </label>
                                <span>Need help? Contact the school admin.</span>
                            </div>

                            <button type="submit" class="primary-button">Log In</button>
                        </form>

                        <p class="demo">
                            Demo account after seeding: <strong>test@example.com</strong> / <strong>password</strong>
                        </p>

                        <div class="signin-link" style="margin-top: 24px; text-align: center; color: var(--muted); font-size: 0.95rem;">
                            Don't have an account? <a href="{{ route('register') }}" style="color: var(--primary); text-decoration: none; transition: color 0.2s ease;">Create one now</a>
                        </div>
                    @endauth
                </section>
            </div>
        </main>
    </body>
</html>
