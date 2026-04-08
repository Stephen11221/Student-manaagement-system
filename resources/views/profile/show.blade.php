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
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:"Instrument Sans",sans-serif; background:linear-gradient(135deg,#020617 0%,#0f172a 54%,#111827 100%); color:#e2e8f0; min-height:100vh; }
        .container { max-width:900px; margin:0 auto; padding:40px 20px; }
        header,.profile-header { display:flex; align-items:center; }
        header { justify-content:space-between; margin-bottom:40px; padding-bottom:20px; border-bottom:1px solid rgba(148,163,184,.1); gap:16px; flex-wrap:wrap; }
        h1 { color:#f8fafc; font-size:2rem; }
        .back-btn,.edit-btn { display:inline-flex; align-items:center; gap:8px; text-decoration:none; font-weight:600; border-radius:8px; transition:.2s; }
        .back-btn { background:rgba(34,211,238,.1); color:#22d3ee; border:1px solid rgba(34,211,238,.3); padding:10px 20px; }
        .profile-card { background:rgba(15,23,42,.78); border:1px solid rgba(148,163,184,.18); border-radius:16px; padding:30px; margin-bottom:30px; backdrop-filter:blur(18px); }
        .profile-header { gap:30px; margin-bottom:30px; padding-bottom:30px; border-bottom:1px solid rgba(148,163,184,.1); }
        .avatar { width:100px; height:100px; background:linear-gradient(135deg,#22d3ee,#06b6d4); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:3rem; font-weight:700; color:#082f49; }
        .profile-info { flex:1; }
        .profile-info h2,.info-value { color:#f8fafc; }
        .profile-info p,.info-label { color:#94a3b8; }
        .info-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:20px; }
        .info-item { background:rgba(34,211,238,.05); border-left:3px solid #22d3ee; padding:16px; border-radius:8px; }
        .info-label { font-size:.85rem; font-weight:600; margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px; }
        .status-badge,.role-badge { display:inline-flex; align-items:center; gap:8px; padding:8px 14px; border-radius:20px; font-size:.85rem; font-weight:600; margin-right:10px; }
        .role-badge { background:rgba(34,211,238,.15); border:1px solid rgba(34,211,238,.3); color:#22d3ee; }
        .status-active { background:rgba(16,185,129,.2); color:#10b981; }
        .status-suspended { background:rgba(239,68,68,.2); color:#ef4444; }
        .edit-btn { background:linear-gradient(135deg,#22d3ee,#06b6d4); color:#082f49; padding:12px 24px; margin-top:20px; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fa-regular fa-user"></i> My Profile</h1>
            <a href="{{ route('dashboard') }}" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
        </header>

        <div class="profile-card">
            <div class="profile-header">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="profile-info">
                    <h2>{{ auth()->user()->name }}</h2>
                    <p>{{ auth()->user()->email }}</p>
                    <div style="margin-top: 10px;">
                        <span class="role-badge">
                            <i class="fa-solid fa-id-badge"></i>
                            {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                        </span>
                        <span class="status-badge {{ auth()->user()->deleted_at ? 'status-suspended' : 'status-active' }}">
                            <i class="fa-solid {{ auth()->user()->deleted_at ? 'fa-lock' : 'fa-circle-check' }}"></i>
                            {{ auth()->user()->deleted_at ? 'SUSPENDED' : 'ACTIVE' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item"><div class="info-label"><i class="fa-regular fa-envelope"></i> Email</div><div class="info-value">{{ auth()->user()->email }}</div></div>
                <div class="info-item"><div class="info-label"><i class="fa-regular fa-user"></i> Full Name</div><div class="info-value">{{ auth()->user()->name }}</div></div>
                <div class="info-item"><div class="info-label"><i class="fa-solid fa-building"></i> Department</div><div class="info-value">{{ auth()->user()->department ?? 'Not assigned' }}</div></div>
                <div class="info-item"><div class="info-label"><i class="fa-regular fa-calendar"></i> Member Since</div><div class="info-value">{{ auth()->user()->created_at->format('M d, Y') }}</div></div>
                <div class="info-item"><div class="info-label"><i class="fa-regular fa-clock"></i> Last Updated</div><div class="info-value">{{ auth()->user()->updated_at->format('M d, Y H:i') }}</div></div>
                <div class="info-item"><div class="info-label"><i class="fa-solid fa-user-tag"></i> Role</div><div class="info-value">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</div></div>
            </div>

            <a href="{{ route('profile.edit') }}" class="edit-btn"><i class="fa-regular fa-pen-to-square"></i> Edit Profile</a>
        </div>
    </div>
</body>
</html>
