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
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:"Instrument Sans",sans-serif; background:linear-gradient(135deg,#020617 0%,#0f172a 54%,#111827 100%); color:#e2e8f0; min-height:100vh; }
        .container { max-width:600px; margin:0 auto; padding:40px 20px; }
        header { margin-bottom:40px; padding-bottom:20px; border-bottom:1px solid rgba(148,163,184,.1); }
        h1 { color:#f8fafc; font-size:2rem; margin-bottom:8px; }
        .subtitle,.help-text { color:#94a3b8; }
        .card { background:rgba(15,23,42,.78); border:1px solid rgba(148,163,184,.18); border-radius:16px; padding:30px; backdrop-filter:blur(18px); }
        .form-section { margin-bottom:28px; }
        .section-title { color:#22d3ee; font-size:.9rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; margin-bottom:16px; padding-bottom:12px; border-bottom:1px solid rgba(34,211,238,.2); display:flex; align-items:center; gap:8px; }
        .form-group { margin-bottom:20px; }
        label { display:block; margin-bottom:8px; color:#dbeafe; font-weight:600; font-size:.95rem; }
        input { width:100%; padding:12px 14px; border:1px solid rgba(148,163,184,.2); border-radius:8px; background:rgba(2,6,23,.56); color:#f8fafc; font-family:inherit; font-size:.95rem; transition:.2s; }
        input:focus { outline:none; border-color:#22d3ee; background:rgba(2,6,23,.78); box-shadow:0 0 0 3px rgba(34,211,238,.1); }
        .password-field { position:relative; }
        .password-field input { padding-right:96px; }
        .password-toggle {
            position:absolute; right:10px; top:38px; border:1px solid rgba(148,163,184,.2);
            background:rgba(2,6,23,.7); color:#dbeafe; border-radius:999px; padding:8px 12px;
            font:inherit; font-size:.82rem; display:inline-flex; align-items:center; gap:8px; width:auto;
        }
        .button-group { display:flex; gap:12px; margin-top:28px; padding-top:24px; border-top:1px solid rgba(148,163,184,.1); }
        .btn { flex:1; padding:12px 24px; border:none; border-radius:8px; cursor:pointer; font-weight:700; font-size:.95rem; transition:.2s; display:flex; align-items:center; justify-content:center; gap:8px; text-decoration:none; }
        .btn-primary { background:linear-gradient(135deg,#22d3ee,#06b6d4); color:#082f49; }
        .btn-secondary { background:rgba(148,163,184,.1); color:#94a3b8; border:1px solid rgba(148,163,184,.2); }
        .alert { padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:.9rem; }
        .alert-success { background:rgba(16,185,129,.15); border:1px solid rgba(16,185,129,.3); color:#10b981; }
        .alert-error { background:rgba(239,68,68,.15); border:1px solid rgba(239,68,68,.3); color:#ef4444; }
        .error-text { color:#ef4444; font-size:.8rem; margin-top:4px; }
        .help-text { font-size:.8rem; margin-top:6px; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fa-regular fa-pen-to-square"></i> Edit Profile</h1>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <p class="subtitle">Update your account information</p>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="max-width:max-content;"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
            </div>
        </header>

        <div class="card">
            @if ($errors->any())
                <div class="alert alert-error">
                    <strong><i class="fa-solid fa-triangle-exclamation"></i> Please fix the errors below:</strong>
                    <ul style="margin-top: 8px; margin-left: 20px;">
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

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <div class="form-section">
                    <div class="section-title"><i class="fa-regular fa-user"></i> Personal Information</div>
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')<div class="error-text">{{ $message }}</div>@enderror
                        <div class="help-text"><i class="fa-regular fa-envelope"></i> Used for login and notifications</div>
                    </div>
                    <div class="form-group">
                        <label for="department">Department</label>
                        <input type="text" id="department" name="department" value="{{ old('department', auth()->user()->department) }}" placeholder="e.g., Engineering, Business">
                        @error('department')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-title"><i class="fa-solid fa-lock"></i> Change Password</div>
                    <div class="form-group password-field">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" placeholder="Leave blank to keep current password">
                        <button type="button" class="password-toggle" data-password-toggle data-show-label="Show" data-hide-label="Hide" aria-label="Show current password" aria-pressed="false">
                            <i class="fa-regular fa-eye"></i> <span data-password-label>Show</span>
                        </button>
                        @error('current_password')<div class="error-text">{{ $message }}</div>@enderror
                        <div class="help-text"><i class="fa-solid fa-circle-info"></i> Required only when changing your password</div>
                    </div>
                    <div class="form-group password-field">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
                        <button type="button" class="password-toggle" data-password-toggle data-show-label="Show" data-hide-label="Hide" aria-label="Show new password" aria-pressed="false">
                            <i class="fa-regular fa-eye"></i> <span data-password-label>Show</span>
                        </button>
                        @error('password')<div class="error-text">{{ $message }}</div>@enderror
                        <div class="help-text"><i class="fa-solid fa-shield-halved"></i> Minimum 8 characters recommended</div>
                    </div>
                    <div class="form-group password-field">
                        <label for="password_confirmation">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password">
                        <button type="button" class="password-toggle" data-password-toggle data-show-label="Show" data-hide-label="Hide" aria-label="Show password confirmation" aria-pressed="false">
                            <i class="fa-regular fa-eye"></i> <span data-password-label>Show</span>
                        </button>
                        @error('password_confirmation')<div class="error-text">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i> Save Changes</button>
                    <a href="{{ route('profile.show') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Cancel</a>
                </div>
            </form>
        </div>
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
