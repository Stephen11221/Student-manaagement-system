<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Register | {{ config('app.name', 'School Portal') }}</title>
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

            .topbar {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                padding: 20px 32px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: rgba(2, 6, 23, 0.6);
                backdrop-filter: blur(8px);
                border-bottom: 1px solid rgba(148, 163, 184, 0.1);
                z-index: 100;
            }

            .logo {
                font-size: 1.3rem;
                font-weight: 700;
                color: var(--heading);
                text-decoration: none;
            }

            .topbar-links {
                display: flex;
                gap: 16px;
                align-items: center;
            }

            .topbar-links a {
                text-decoration: none;
                color: var(--muted);
                font-size: 0.95rem;
                transition: color 0.2s ease;
            }

            .topbar-links a:hover {
                color: var(--heading);
            }

            .layout {
                display: grid;
                gap: 32px;
                grid-template-columns: minmax(0, 1.15fr) minmax(320px, 0.85fr);
                align-items: center;
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

            .panel {
                border: 1px solid var(--panel-border);
                background: var(--panel);
                backdrop-filter: blur(18px);
                box-shadow: 0 24px 80px rgba(2, 6, 23, 0.32);
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

            input[type="text"],
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

            input[type="text"]:focus,
            input[type="email"]:focus,
            input[type="password"]:focus {
                border-color: rgba(34, 211, 238, 0.65);
                transform: translateY(-1px);
            }

            input.error {
                border-color: rgba(251, 113, 133, 0.5);
                background: rgba(244, 63, 94, 0.08);
            }

            .field-error {
                margin-top: 6px;
                font-size: 0.85rem;
                color: #fca5ac;
            }

            .actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                color: var(--muted);
                font-size: 0.9rem;
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

            .signin-link {
                margin-top: 20px;
                text-align: center;
                color: var(--muted);
                font-size: 0.95rem;
            }

            .signin-link a {
                color: var(--primary);
                text-decoration: none;
                transition: color 0.2s ease;
            }

            .signin-link a:hover {
                color: #cffafe;
            }

            @media (max-width: 980px) {
                .layout {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 640px) {
                .shell {
                    width: min(100% - 24px, 1180px);
                    padding: 100px 0 28px 0;
                }

                .topbar {
                    padding: 16px 20px;
                }

                .panel {
                    padding: 24px;
                }

                .actions {
                    flex-direction: column;
                    align-items: stretch;
                }

                .layout {
                    gap: 24px;
                }
            }
        </style>
    </head>
    <body>
        <header class="topbar">
            <a href="{{ route('welcome') }}" class="logo">School Portal</a>
            <nav class="topbar-links">
                @auth
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                @endauth
                <a href="{{ route('welcome') }}">Home</a>
                <a href="{{ route('login') }}">Sign In</a>
            </nav>
        </header>

        <main class="shell">
            <div class="layout">
                <section class="hero">
                    <h1>Create Your Account</h1>
                    <p class="lead">
                        Join the School Portal and get access to grades, attendance, communication, and more. Creating an account takes just a minute.
                    </p>
                </section>

                <section class="panel">
                    <p class="panel-tag">New account</p>
                    <h2>Register Now</h2>
                    <p class="panel-copy">
                        Fill in the details below to create your account.
                    </p>

                    @if ($errors->any())
                        <div class="alert error">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register.store') }}">
                        @csrf

                        <div class="field">
                            <label for="name">Full Name</label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                placeholder="John Doe"
                                required
                                autofocus
                                class="{{ $errors->has('name') ? 'error' : '' }}"
                            >
                            @if ($errors->has('name'))
                                <div class="field-error">{{ $errors->first('name') }}</div>
                            @endif
                        </div>

                        <div class="field">
                            <label for="email">Email address</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                placeholder="student@schoolportal.com"
                                required
                                class="{{ $errors->has('email') ? 'error' : '' }}"
                            >
                            @if ($errors->has('email'))
                                <div class="field-error">{{ $errors->first('email') }}</div>
                            @endif
                        </div>

                        <div class="field">
                            <label for="password">Password</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="Create a strong password"
                                required
                                class="{{ $errors->has('password') ? 'error' : '' }}"
                            >
                            @if ($errors->has('password'))
                                <div class="field-error">{{ $errors->first('password') }}</div>
                            @endif
                        </div>

                        <div class="field">
                            <label for="password_confirmation">Confirm Password</label>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                placeholder="Confirm your password"
                                required
                            >
                        </div>

                        <button type="submit" class="primary-button">Create Account</button>

                        <div class="signin-link">
                            Already have an account? <a href="{{ route('login') }}">Sign in here</a>
                        </div>
                    </form>
                </section>
            </div>
        </main>
    </body>
</html>
