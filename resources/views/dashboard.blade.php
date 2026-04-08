<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard | {{ config('app.name', 'School Portal') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        <style>
            :root {
                color-scheme: dark;
                --bg: #020617;
                --text: #e2e8f0;
                --muted: #94a3b8;
                --heading: #f8fafc;
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
                    radial-gradient(circle at top left, rgba(34, 211, 238, 0.18), transparent 25%),
                    linear-gradient(135deg, #020617 0%, #0f172a 56%, #111827 100%);
            }

            .shell {
                width: min(1120px, calc(100% - 32px));
                margin: 0 auto;
                padding: 40px 0;
            }

            .panel {
                padding: 34px;
                border-radius: 34px;
                border: 1px solid rgba(148, 163, 184, 0.18);
                background: linear-gradient(135deg, rgba(8, 47, 73, 0.92), rgba(15, 23, 42, 0.96), rgba(6, 78, 59, 0.92));
                box-shadow: 0 24px 80px rgba(2, 6, 23, 0.35);
            }

            .topbar {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 20px;
            }

            .tag {
                margin: 0;
                text-transform: uppercase;
                letter-spacing: 0.22em;
                font-size: 0.8rem;
                font-weight: 700;
                color: #bae6fd;
            }

            h1,
            p {
                margin: 0;
            }

            h1 {
                margin-top: 12px;
                font-size: clamp(2.2rem, 4vw, 3.6rem);
                color: var(--heading);
            }

            .intro {
                margin-top: 14px;
                max-width: 740px;
                line-height: 1.75;
                color: var(--muted);
            }

            .button-row {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
            }

            a,
            button {
                text-decoration: none;
                font: inherit;
                border: 0;
                cursor: pointer;
                padding: 14px 18px;
                border-radius: 18px;
                font-weight: 700;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: transform 0.2s ease, opacity 0.2s ease;
            }

            a:hover,
            button:hover {
                transform: translateY(-1px);
            }

            .secondary {
                color: var(--heading);
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(148, 163, 184, 0.18);
            }

            .primary {
                color: #082f49;
                background: linear-gradient(135deg, #22d3ee, #06b6d4);
            }

            .stats {
                margin-top: 30px;
                display: grid;
                gap: 16px;
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .stat {
                border-radius: 26px;
                border: 1px solid rgba(148, 163, 184, 0.14);
                background: rgba(255, 255, 255, 0.05);
                padding: 22px;
            }

            .stat p {
                line-height: 1.6;
            }

            .stat .label {
                font-size: 0.92rem;
                color: #bae6fd;
            }

            .stat .value {
                margin: 10px 0 4px;
                font-size: 2rem;
                font-weight: 700;
                color: var(--heading);
            }

            .stat .copy {
                color: var(--muted);
                font-size: 0.94rem;
            }

            @media (max-width: 860px) {
                .topbar {
                    flex-direction: column;
                }

                .stats {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 640px) {
                .shell {
                    width: min(100% - 24px, 1120px);
                    padding: 24px 0;
                }

                .panel {
                    padding: 24px;
                }

                .button-row {
                    width: 100%;
                    flex-direction: column;
                }
            }
        </style>
    </head>
    <body>
        <main class="shell">
            <section class="panel">
                <div class="topbar">
                    <div>
                        <p class="tag">Dashboard</p>
                        <h1>Hello, {{ auth()->user()->name }}</h1>
                        <p class="intro">
                            You are signed in to the school portal. This dashboard is ready for the next modules you want to add, including students, classes, grades, fees, or attendance.
                        </p>
                    </div>

                    <div class="button-row">
                        <a href="{{ route('welcome') }}" class="secondary">Back to Home</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="primary">Log Out</button>
                        </form>
                    </div>
                </div>

                <div class="stats">
                    <article class="stat">
                        <p class="label">Classes</p>
                        <p class="value">12</p>
                        <p class="copy">Active classes currently visible in your portal.</p>
                    </article>
                    <article class="stat">
                        <p class="label" style="color: #a7f3d0;">Attendance</p>
                        <p class="value">96%</p>
                        <p class="copy">Average attendance snapshot for this term.</p>
                    </article>
                    <article class="stat">
                        <p class="label" style="color: #fde68a;">Notices</p>
                        <p class="value">4</p>
                        <p class="copy">Unread announcements waiting for your review.</p>
                    </article>
                </div>
            </section>
        </main>
    </body>
</html>
