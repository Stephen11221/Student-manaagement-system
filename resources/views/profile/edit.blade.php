<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profile | {{ config('app.name', 'School Portal') }}</title>
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
        h1,p { margin:0; }
        .page-title { margin-top:12px; font-size:clamp(2rem,3.4vw,3rem); line-height:1.05; font-weight:800; }
        .page-copy { margin-top:12px; max-width:72ch; color:var(--muted); line-height:1.7; }
        .surface {
            background:var(--surface); border:1px solid var(--border); border-radius:28px;
            box-shadow:0 24px 80px rgba(2,6,23,.34); backdrop-filter:blur(18px); padding:24px;
        }
        .section { margin-bottom:24px; }
        .section-title { display:flex; align-items:center; gap:10px; font-size:1.05rem; font-weight:800; color:var(--text); }
        .section-subtitle { margin-top:6px; color:var(--muted); }
        .alert {
            padding:14px 16px; border-radius:16px; margin-bottom:18px; font-size:.95rem; line-height:1.6;
        }
        .alert-success {
            background:rgba(34,197,94,.14); border:1px solid rgba(34,197,94,.28); color:#bbf7d0;
        }
        .alert-error {
            background:rgba(239,68,68,.14); border:1px solid rgba(239,68,68,.28); color:#fecaca;
        }
        .grid {
            display:grid; gap:16px; grid-template-columns:repeat(2, minmax(0,1fr));
        }
        .card {
            display:grid; gap:18px;
            background:var(--surface-strong); border:1px solid var(--border); border-radius:24px; padding:22px;
        }
        .field { display:grid; gap:8px; }
        label { color:#e2e8f0; font-weight:700; font-size:.95rem; }
        input {
            width:100%; padding:12px 14px; border:1px solid var(--border); border-radius:14px;
            background:rgba(2,6,23,.62); color:var(--text); font:inherit; font-size:.95rem; transition:.2s;
        }
        input:focus { outline:none; border-color:rgba(56,189,248,.65); box-shadow:0 0 0 3px rgba(56,189,248,.12); }
        .help-text { color:var(--muted); font-size:.82rem; line-height:1.5; }
        .error-text { color:#fecaca; font-size:.82rem; margin-top:4px; }
        .password-field { position:relative; }
        .password-field input { padding-right:98px; }
        .password-toggle {
            position:absolute; right:10px; top:38px; border:1px solid var(--border); background:rgba(2,6,23,.72);
            color:#dbeafe; border-radius:999px; padding:8px 12px; font:inherit; font-size:.82rem;
            display:inline-flex; align-items:center; gap:8px; width:auto;
        }
        .button-group { display:flex; gap:12px; margin-top:24px; }
        .btn {
            flex:1; display:inline-flex; align-items:center; justify-content:center; gap:8px;
            padding:12px 20px; border-radius:16px; text-decoration:none; border:1px solid transparent;
            font-weight:800; transition:transform 160ms ease, background 160ms ease, border-color 160ms ease;
        }
        .btn:hover { transform:translateY(-1px); }
        .btn-primary { background:linear-gradient(135deg,var(--primary),var(--primary-strong)); color:#082f49; }
        .btn-secondary { background:rgba(15,23,42,.92); color:var(--text); border-color:var(--border); }
        .summary {
            background:rgba(6,182,212,.1); border:1px solid rgba(6,182,212,.28); border-radius:20px; padding:16px;
        }
        .summary-title { display:flex; align-items:center; gap:10px; font-weight:800; color:var(--text); }
        .summary-copy { margin-top:8px; color:var(--muted); line-height:1.6; }
        @media (max-width: 1000px) {
            .page-header, .grid { grid-template-columns:1fr; }
        }
        @media (max-width: 640px) {
            .page-shell { width:calc(100% - 24px); padding-top:24px; }
            .button-group { flex-direction:column; }
            .btn { width:100%; }
            .surface, .card { padding:18px; }
        }
    </style>
</head>
<body>
    <div class="page-shell">
        <header class="page-header">
            <div>
                <p class="eyebrow"><i class="fa-regular fa-pen-to-square"></i> Profile settings</p>
                <h1 class="page-title">Edit Profile</h1>
                <p class="page-copy">Update your account information, password, and contact details in one place. Fields are grouped to keep the form easy to scan and less error-prone on small screens.</p>
            </div>
            <div>
                <a href="{{ route('profile.show') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back to Profile</a>
            </div>
        </header>

        @if ($errors->any())
            <div class="alert alert-error">
                <strong><i class="fa-solid fa-triangle-exclamation"></i> Please fix the errors below:</strong>
                <ul style="margin-top:8px; margin-left:20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" class="grid">
            @csrf
            @method('PUT')

            <section class="card">
                <div class="section">
                    <div class="section-title"><i class="fa-regular fa-address-card"></i> Personal information</div>
                    <div class="section-subtitle">Identity and contact fields.</div>
                </div>
                <div class="field">
                    <label for="name"><i class="fa-regular fa-user"></i> Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                    @error('name')<div class="error-text">{{ $message }}</div>@enderror
                </div>
                <div class="field">
                    <label for="email"><i class="fa-regular fa-envelope"></i> Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                    @error('email')<div class="error-text">{{ $message }}</div>@enderror
                    <div class="help-text">Used for login and notifications.</div>
                </div>
                <div class="field">
                    <label for="department"><i class="fa-solid fa-building"></i> Department</label>
                    <input type="text" id="department" name="department" value="{{ old('department', auth()->user()->department) }}" placeholder="e.g., Engineering, Business">
                    @error('department')<div class="error-text">{{ $message }}</div>@enderror
                </div>
            </section>

            <section class="card">
                <div class="section">
                    <div class="section-title"><i class="fa-solid fa-lock"></i> Change password</div>
                    <div class="section-subtitle">Only fill this section if you want to update your password.</div>
                </div>

                <div class="field password-field">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" placeholder="Leave blank to keep current password">
                    <button type="button" class="password-toggle" data-password-toggle data-show-label="Show" data-hide-label="Hide" aria-label="Show current password" aria-pressed="false">
                        <i class="fa-regular fa-eye"></i> <span data-password-label>Show</span>
                    </button>
                    @error('current_password')<div class="error-text">{{ $message }}</div>@enderror
                    <div class="help-text">Required only when changing your password.</div>
                </div>

                <div class="field password-field">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
                    <button type="button" class="password-toggle" data-password-toggle data-show-label="Show" data-hide-label="Hide" aria-label="Show new password" aria-pressed="false">
                        <i class="fa-regular fa-eye"></i> <span data-password-label>Show</span>
                    </button>
                    @error('password')<div class="error-text">{{ $message }}</div>@enderror
                    <div class="help-text">Use at least 8 characters with a mix of letters and numbers.</div>
                </div>

                <div class="field password-field">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password">
                    <button type="button" class="password-toggle" data-password-toggle data-show-label="Show" data-hide-label="Hide" aria-label="Show password confirmation" aria-pressed="false">
                        <i class="fa-regular fa-eye"></i> <span data-password-label>Show</span>
                    </button>
                    @error('password_confirmation')<div class="error-text">{{ $message }}</div>@enderror
                </div>

                <div class="summary">
                    <div class="summary-title"><i class="fa-solid fa-shield-halved"></i> Security note</div>
                    <div class="summary-copy">Password changes take effect immediately after save. If you are on a shared device, make sure you sign out after updating your account.</div>
                </div>
            </section>

            <div style="grid-column:1 / -1;">
                <div class="button-group">
                    <button type="submit" class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i> Save Changes</button>
                    <a href="{{ route('profile.show') }}" class="btn btn-secondary"><i class="fa-solid fa-xmark"></i> Cancel</a>
                </div>
            </div>
        </form>
    </div>

    @include('partials.idle-timeout-modal')
    <script src="{{ asset('js/idle-timeout.js') }}"></script>
    <script src="{{ asset('js/password-toggle.js') }}"></script>
    <script>
        document.documentElement.dataset.idleTimeout = "{{ config('idle.idle_timeout', 15) }}";
        document.documentElement.dataset.warningTime = "{{ config('idle.warning_time', 1) }}";
    </script>
</body>
</html>
