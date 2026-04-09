<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Career Coach Dashboard | {{ config('app.name', 'School Portal') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            :root { color-scheme: dark; --text:#e2e8f0; --muted:#94a3b8; --heading:#f8fafc; --primary:#22d3ee; --success:#34d399; }
            * { box-sizing:border-box; margin:0; padding:0; }
            body { font-family:"Instrument Sans",sans-serif; color:var(--text); background:linear-gradient(135deg,#020617 0%,#0f172a 54%,#111827 100%); min-height:100vh; }
            .container { max-width:1200px; margin:0 auto; padding:40px 20px; }
            header,.toolbar,.student-meta { display:flex; align-items:center; }
            header { justify-content:space-between; margin-bottom:40px; padding-bottom:20px; border-bottom:1px solid rgba(148,163,184,.1); gap:16px; flex-wrap:wrap; }
            h1 { font-size:2.5rem; color:var(--heading); }
            .toolbar { gap:8px; flex-wrap:wrap; }
            .nav-btn,.logout-btn,.action-btn { border-radius:8px; text-decoration:none; font-weight:700; transition:.2s; display:inline-flex; align-items:center; gap:8px; }
            .nav-btn,.logout-btn { padding:10px 20px; }
            .nav-btn { background:rgba(34,211,238,.1); color:#22d3ee; border:1px solid rgba(34,211,238,.3); }
            .nav-btn:hover,.action-btn:hover { background:rgba(34,211,238,.2); }
            .logout-btn { background:linear-gradient(135deg,#22d3ee,#06b6d4); color:#082f49; border:none; cursor:pointer; }
            .grid,.feature-grid,.student-list { display:grid; gap:24px; }
            .grid { grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); margin-bottom:40px; }
            .feature-grid { grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); }
            .student-list { grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); }
            .card,.feature-card,.student-card { background:rgba(15,23,42,.78); border:1px solid rgba(148,163,184,.18); border-radius:16px; padding:24px; backdrop-filter:blur(18px); }
            .feature-card { background:rgba(52,211,153,.08); border-color:rgba(52,211,153,.25); }
            .metric-icon,.feature-icon { width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; margin-bottom:16px; }
            .metric-icon { background:rgba(34,211,238,.14); color:var(--primary); font-size:1.2rem; }
            .feature-icon { background:rgba(52,211,153,.14); color:var(--success); font-size:1.1rem; }
            .stat-number { font-size:2rem; color:var(--primary); font-weight:700; }
            .stat-label,.student-card p,.card p,.feature-card p,.empty-state p { color:var(--muted); line-height:1.6; }
            .section-title { font-size:1.75rem; color:var(--heading); margin:40px 0 24px; display:flex; align-items:center; gap:12px; }
            .card h2,.feature-card h3,.student-card h3 { color:var(--heading); margin-bottom:12px; }
            .student-meta { justify-content:space-between; color:var(--muted); font-size:.9rem; margin:10px 0; }
            .action-btn { background:var(--primary); color:#082f49; padding:10px 16px; margin-top:14px; }
            .empty-state { text-align:center; padding:40px; color:var(--muted); background:rgba(15,23,42,.4); border:1px dashed rgba(148,163,184,.2); border-radius:16px; }
        </style>
    </head>
    <body>
        <div class="container">
            <header>
                <div>
                    <h1><i class="fa-solid fa-compass-drafting"></i> Career Guidance Portal</h1>
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

            <div class="grid">
                <div class="card">
                    <div class="metric-icon"><i class="fa-solid fa-user-group"></i></div>
                    <div class="stat-number">{{ $students->count() }}</div>
                    <div class="stat-label">Students currently assigned to you</div>
                </div>
                <div class="card">
                    <div class="metric-icon"><i class="fa-solid fa-road"></i></div>
                    <h2>Career planning</h2>
                    <p>Guide students through pathways, skills planning, and next-step decisions with a dedicated caseload.</p>
                </div>
                <div class="card">
                    <div class="metric-icon"><i class="fa-solid fa-chart-line"></i></div>
                    <h2>Progress tracking</h2>
                    <p>Review enrolled classes and homework activity to support better coaching conversations.</p>
                </div>
            </div>

            <div class="section-title"><i class="fa-solid fa-briefcase"></i> Career Coach Features</div>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                    <h3>Career counseling</h3>
                    <p>Support students with pathway exploration, goal setting, and long-term planning.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-chart-column"></i></div>
                    <h3>Performance analysis</h3>
                    <p>Use classes and submission activity to spot students who need stronger guidance or intervention.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                    <h3>Skill development</h3>
                    <p>Recommend growth areas and practical learning opportunities aligned to target careers.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fa-solid fa-handshake-angle"></i></div>
                    <h3>Mentor connections</h3>
                    <p>Prepare students for mentorship, internships, and career readiness conversations.</p>
                </div>
            </div>

            <div class="section-title"><i class="fa-solid fa-users-viewfinder"></i> Students Under Your Guidance</div>
            @if($students->count() > 0)
                <div class="student-list">
                    @foreach($students as $student)
                        <div class="student-card">
                            <h3>{{ $student->name }}</h3>
                            <p><i class="fa-regular fa-envelope"></i> {{ $student->email }}</p>
                            <div class="student-meta">
                                <span><i class="fa-solid fa-book"></i> {{ $student->enrolledClasses->count() }} classes</span>
                                <span><i class="fa-solid fa-file-circle-check"></i> {{ $student->homeworkSubmissions->count() }} submissions</span>
                            </div>
                            <p><i class="fa-solid fa-building"></i> {{ $student->department ?: 'No department assigned' }}</p>
                            <a href="{{ route('career-coach.students.show', $student) }}" class="action-btn"><i class="fa-solid fa-user-check"></i> Review Student</a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <h3><i class="fa-regular fa-folder-open"></i> No students assigned</h3>
                    <p>Assign students to a career coach from the admin user management screen.</p>
                </div>
            @endif
        </div>
    
    @include('partials.idle-timeout-modal')
    <script src="{{ asset('js/idle-timeout.js') }}"></script>
    <script>
        document.documentElement.dataset.idleTimeout = "{{ config('idle.idle_timeout', 15) }}";
        document.documentElement.dataset.warningTime = "{{ config('idle.warning_time', 1) }}";
    </script>
    </body>
</html>
