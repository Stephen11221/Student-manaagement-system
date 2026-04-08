<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin Dashboard | {{ config('app.name', 'School Portal') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            :root { color-scheme: dark; --text:#e2e8f0; --muted:#94a3b8; --heading:#f8fafc; --primary:#22d3ee; }
            * { box-sizing:border-box; margin:0; padding:0; }
            body { font-family:"Instrument Sans",sans-serif; color:var(--text); background:linear-gradient(135deg,#020617 0%,#0f172a 54%,#111827 100%); min-height:100vh; }
            .container { max-width:1200px; margin:0 auto; padding:40px 20px; }
            header,.stats-grid,.admin-features { display:grid; }
            header { grid-template-columns:1fr auto; gap:16px; align-items:center; margin-bottom:40px; padding-bottom:20px; border-bottom:1px solid rgba(148,163,184,.1); }
            h1 { font-size:2.5rem; color:var(--heading); }
            .toolbar { display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end; }
            .nav-btn,.logout-btn { display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:700; transition:.2s; }
            .nav-btn { background:rgba(34,211,238,.1); color:#22d3ee; border:1px solid rgba(34,211,238,.3); }
            .logout-btn { background:linear-gradient(135deg,#22d3ee,#06b6d4); color:#082f49; border:none; cursor:pointer; }
            .stats-grid,.admin-features { grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:24px; }
            .stats-grid { margin-bottom:40px; }
            .stat-card,.feature-box,.quick-link { background:rgba(15,23,42,.78); border:1px solid rgba(148,163,184,.18); border-radius:16px; padding:24px; backdrop-filter:blur(18px); }
            .stat-number { font-size:2.4rem; color:var(--primary); font-weight:700; margin-bottom:8px; }
            .section-title { font-size:1.75rem; color:var(--heading); margin:40px 0 24px; display:flex; align-items:center; gap:10px; }
            .quick-links { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:20px; margin-bottom:30px; }
            .quick-link i,.feature-box i { color:var(--primary); }
            .quick-link h3,.feature-box h3 { color:var(--heading); margin:12px 0; }
            .quick-link p,.feature-box p,.feature-box li,.stat-label { color:var(--muted); line-height:1.6; }
            .quick-link a { display:inline-flex; align-items:center; gap:8px; margin-top:16px; padding:10px 16px; border-radius:8px; background:var(--primary); color:#082f49; text-decoration:none; font-weight:700; }
            .feature-box ul { list-style:none; margin-top:12px; }
            .feature-box li { padding:4px 0; }
        </style>
    </head>
    <body>
        <div class="container">
            <header>
                <div>
                    <h1><i class="fa-solid fa-shield-halved"></i> System Administration</h1>
                    <p style="color: var(--muted); margin-top: 8px;">Welcome, {{ auth()->user()->name }}</p>
                </div>
                <div class="toolbar">
                    <a href="{{ route('notifications.index') }}" class="nav-btn"><i class="fa-regular fa-bell"></i> Notifications</a>
                    <a href="{{ route('profile.show') }}" class="nav-btn"><i class="fa-regular fa-user"></i> Profile</a>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Log Out</button>
                    </form>
                </div>
            </header>

            <div class="stats-grid">
                <div class="stat-card"><div class="stat-number">{{ $totalUsers }}</div><div class="stat-label">Total users</div></div>
                <div class="stat-card"><div class="stat-number">{{ $students }}</div><div class="stat-label">Students</div></div>
                <div class="stat-card"><div class="stat-number">{{ $trainers }}</div><div class="stat-label">Trainers</div></div>
            </div>

            <div class="section-title"><i class="fa-solid fa-sliders"></i> Admin Control Center</div>
            <div class="quick-links">
                <div class="quick-link"><i class="fa-solid fa-users fa-2x"></i><h3>Manage users</h3><p>Create, edit, suspend, activate, and assign users to departments or career coaches.</p><a href="{{ route('admin.users.index') }}"><i class="fa-solid fa-arrow-right"></i> Open users</a></div>
                <div class="quick-link"><i class="fa-solid fa-user-plus fa-2x"></i><h3>Add new user</h3><p>Create accounts for students, trainers, admins, and career coaches.</p><a href="{{ route('admin.users.create') }}"><i class="fa-solid fa-plus"></i> Create account</a></div>
                <div class="quick-link"><i class="fa-solid fa-school fa-2x"></i><h3>View classes</h3><p>Review all classes, trainers, student counts, and homework coverage.</p><a href="{{ route('admin.classes.index') }}"><i class="fa-solid fa-eye"></i> Open classes</a></div>
                <div class="quick-link"><i class="fa-solid fa-book-open-reader fa-2x"></i><h3>Manage homework</h3><p>Create and update assignments centrally across the school portal.</p><a href="{{ route('admin.homework.index') }}"><i class="fa-solid fa-list-check"></i> Open homework</a></div>
            </div>

            <div class="section-title"><i class="fa-solid fa-gears"></i> Settings and Features</div>
            <div class="admin-features">
                <div class="feature-box">
                    <h3><i class="fa-solid fa-user-gear"></i> User management</h3>
                    <p>Control account access, roles, departments, and career coach assignments.</p>
                    <ul><li>Account lifecycle management</li><li>Role assignments</li><li>Department organization</li><li>Career coach matching</li></ul>
                </div>
                <div class="feature-box">
                    <h3><i class="fa-solid fa-chalkboard-user"></i> Academic oversight</h3>
                    <p>Monitor classes, homework volume, and platform adoption from one place.</p>
                    <ul><li>Class listings</li><li>Trainer oversight</li><li>Homework administration</li><li>Student assignment visibility</li></ul>
                </div>
                <div class="feature-box">
                    <h3><i class="fa-solid fa-chart-line"></i> Analytics</h3>
                    <p>Track high-level user counts and prepare for richer reporting modules.</p>
                    <ul><li>User growth</li><li>Role distribution</li><li>Attendance readiness</li><li>Homework activity</li></ul>
                </div>
            </div>
        </div>
    </body>
</html>
