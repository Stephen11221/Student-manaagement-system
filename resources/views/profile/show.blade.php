<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Profile | {{ config('app.name', 'School Portal') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --surface: rgba(15, 23, 42, 0.94);
            --surface-strong: rgba(30, 41, 59, 0.98);
            --border: rgba(51, 65, 85, 0.95);
            --text: #f8fafc;
            --muted: #cbd5e1;
            --primary: #38bdf8;
            --primary-strong: #0ea5e9;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        * { box-sizing:border-box; margin:0; padding:0; }
        body {
            font-family:"Instrument Sans",sans-serif;
            background:
                radial-gradient(circle at top left, rgba(56, 189, 248, 0.14), transparent 24%),
                radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.1), transparent 22%),
                linear-gradient(135deg, #020617 0%, #0f172a 56%, #111827 100%);
            color:var(--text);
            min-height:100vh;
        }

        .page-shell { width:min(1440px, calc(100% - 32px)); margin:0 auto; padding:40px 0 48px; }
        .page-header {
            display:grid; gap:20px; grid-template-columns:minmax(0,1fr) auto;
            align-items:end; padding-bottom:24px; border-bottom:1px solid var(--border); margin-bottom:24px;
        }
        .eyebrow {
            display:inline-flex; align-items:center; gap:8px; border-radius:999px; border:1px solid rgba(56,189,248,.28);
            background:rgba(56,189,248,.12); color:#cffafe; padding:8px 12px; font-size:.78rem; font-weight:800;
            letter-spacing:.18em; text-transform:uppercase;
        }
        h1,h2,p { margin:0; }
        .page-title { margin-top:12px; font-size:clamp(2rem,3.4vw,3rem); line-height:1.05; font-weight:800; }
        .page-copy { margin-top:12px; max-width:72ch; color:var(--muted); line-height:1.7; }
        .btn {
            display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:16px;
            padding:12px 16px; border:1px solid transparent; text-decoration:none; font-weight:800;
            transition:transform 160ms ease, background 160ms ease, border-color 160ms ease;
        }
        .btn:hover { transform:translateY(-1px); }
        .btn-primary { background:linear-gradient(135deg,var(--primary),var(--primary-strong)); color:#082f49; }
        .btn-secondary { background:rgba(15,23,42,.92); color:var(--text); border-color:var(--border); }
        .surface {
            background:var(--surface); border:1px solid var(--border); border-radius:28px;
            box-shadow:0 24px 80px rgba(2,6,23,.34); backdrop-filter:blur(18px);
        }
        .hero { padding:24px; }
        .hero-top {
            display:flex; gap:20px; align-items:flex-start; justify-content:space-between; flex-wrap:wrap;
            padding-bottom:22px; border-bottom:1px solid var(--border);
        }
        .avatar {
            width:112px; height:112px; border-radius:32px;
            background:linear-gradient(135deg, #38bdf8, #0ea5e9); color:#082f49;
            display:grid; place-items:center; font-size:3rem; font-weight:900;
            box-shadow:0 16px 32px rgba(14,165,233,.2);
            flex:0 0 auto;
        }
        .hero-name { font-size:clamp(1.8rem, 3vw, 2.7rem); font-weight:800; color:var(--text); }
        .hero-email { margin-top:8px; color:var(--muted); }
        .pill-row { display:flex; flex-wrap:wrap; gap:10px; margin-top:14px; }
        .pill {
            display:inline-flex; align-items:center; gap:8px; border-radius:999px; padding:8px 12px;
            font-size:.82rem; font-weight:800; border:1px solid transparent;
        }
        .pill-role { background:rgba(56,189,248,.14); color:#cffafe; border-color:rgba(56,189,248,.28); }
        .pill-active { background:rgba(34,197,94,.14); color:#bbf7d0; border-color:rgba(34,197,94,.28); }
        .pill-suspended { background:rgba(239,68,68,.14); color:#fecaca; border-color:rgba(239,68,68,.28); }
        .section {
            margin-top:24px; padding:24px; background:var(--surface-strong); border:1px solid var(--border); border-radius:24px;
        }
        .section-head { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:18px; }
        .section-title { display:flex; align-items:center; gap:10px; font-size:1.05rem; font-weight:800; color:var(--text); }
        .section-subtitle { margin-top:6px; color:var(--muted); }
        .info-grid { display:grid; gap:16px; grid-template-columns:repeat(3, minmax(0,1fr)); }
        .info-item {
            background:rgba(2,6,23,.4); border:1px solid var(--border); border-radius:20px; padding:16px;
        }
        .info-label {
            display:flex; align-items:center; gap:8px; color:#cbd5e1; font-size:.78rem; font-weight:800; text-transform:uppercase; letter-spacing:.12em;
        }
        .info-value { margin-top:10px; color:var(--text); font-size:1rem; font-weight:700; line-height:1.5; }
        .summary-grid { display:grid; gap:16px; grid-template-columns:repeat(2, minmax(0,1fr)); }
        .summary-card {
            padding:18px; border-radius:20px; background:rgba(2,6,23,.4); border:1px solid var(--border);
        }
        .summary-card .label { color:var(--muted); font-size:.78rem; font-weight:800; text-transform:uppercase; letter-spacing:.12em; }
        .summary-card .value { margin-top:8px; color:var(--text); font-size:1.05rem; font-weight:700; }
        .actions { margin-top:24px; display:flex; gap:12px; flex-wrap:wrap; }
        @media (max-width: 1000px) {
            .page-header { grid-template-columns:1fr; }
            .info-grid, .summary-grid { grid-template-columns:1fr; }
            .hero-top { flex-direction:column; }
        }
        @media (max-width: 640px) {
            .page-shell { width:calc(100% - 24px); padding-top:24px; }
            .actions .btn { width:100%; }
            .surface, .section { padding:18px; }
        }
    </style>
</head>
<body>
    <div class="page-shell">
        <header class="page-header">
            <div>
                <p class="eyebrow"><i class="fa-regular fa-id-card"></i> Profile</p>
                <h1 class="page-title">My Profile</h1>
                <p class="page-copy">Your profile is organized into identity, academic, and account status sections so it is easy to scan and easy to update.</p>
            </div>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
            </div>
        </header>

        <section class="surface hero">
            <div class="hero-top">
                <div style="display:flex; gap:20px; align-items:flex-start; flex-wrap:wrap;">
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div>
                        <div class="hero-name">{{ auth()->user()->name }}</div>
                        <div class="hero-email">{{ auth()->user()->email }}</div>
                        <div class="pill-row">
                            <span class="pill pill-role"><i class="fa-solid fa-user-tag"></i> {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</span>
                            <span class="pill {{ auth()->user()->deleted_at ? 'pill-suspended' : 'pill-active' }}">
                                <i class="fa-solid {{ auth()->user()->deleted_at ? 'fa-lock' : 'fa-circle-check' }}"></i>
                                {{ auth()->user()->deleted_at ? 'Suspended' : 'Active' }}
                            </span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary"><i class="fa-regular fa-pen-to-square"></i> Edit Profile</a>
            </div>

            <div class="section">
                <div class="section-head">
                    <div>
                        <div class="section-title"><i class="fa-regular fa-address-card"></i> Personal information</div>
                        <div class="section-subtitle">Identity and contact details.</div>
                    </div>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label"><i class="fa-regular fa-envelope"></i> Email</div>
                        <div class="info-value">{{ auth()->user()->email }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fa-regular fa-user"></i> Full name</div>
                        <div class="info-value">{{ auth()->user()->name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fa-solid fa-building"></i> Department</div>
                        <div class="info-value">{{ auth()->user()->department ?? 'Not assigned' }}</div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-head">
                    <div>
                        <div class="section-title"><i class="fa-solid fa-graduation-cap"></i> Academic information</div>
                        <div class="section-subtitle">Membership and account timeline.</div>
                    </div>
                </div>
                <div class="summary-grid">
                    <div class="summary-card">
                        <div class="label">Member since</div>
                        <div class="value">{{ auth()->user()->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="label">Last updated</div>
                        <div class="value">{{ auth()->user()->updated_at->format('M d, Y H:i') }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="label">Role</div>
                        <div class="value">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</div>
                    </div>
                    <div class="summary-card">
                        <div class="label">Account status</div>
                        <div class="value">{{ auth()->user()->deleted_at ? 'Suspended' : 'Active' }}</div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('partials.idle-timeout-modal')
    <script src="{{ asset('js/idle-timeout.js') }}"></script>
    <script>
        document.documentElement.dataset.idleTimeout = "{{ config('idle.idle_timeout', 15) }}";
        document.documentElement.dataset.warningTime = "{{ config('idle.warning_time', 1) }}";
    </script>
</body>
</html>
