<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Trainer Dashboard | {{ config('app.name', 'School Portal') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            :root { color-scheme: dark; --text:#e2e8f0; --muted:#94a3b8; --heading:#f8fafc; --primary:#22d3ee; --success:#34d399; }
            * { box-sizing:border-box; margin:0; padding:0; }
            body { font-family:"Instrument Sans",sans-serif; color:var(--text); background:linear-gradient(135deg,#020617 0%,#0f172a 54%,#111827 100%); min-height:100vh; }
            .container { max-width:1200px; margin:0 auto; padding:40px 20px; }
            header,.user-info,.list-item,.action-buttons { display:flex; align-items:center; }
            header { justify-content:space-between; margin-bottom:40px; padding-bottom:20px; border-bottom:1px solid rgba(148,163,184,.1); gap:16px; flex-wrap:wrap; }
            h1 { font-size:2.5rem; color:var(--heading); }
            .user-info,.action-buttons { gap:8px; flex-wrap:wrap; }
            .toolbar-link,.logout-btn,.card-link,.action-btn { display:inline-flex; align-items:center; gap:8px; border-radius:8px; font-weight:700; text-decoration:none; transition:.2s; }
            .toolbar-link { background:rgba(34,211,238,.1); color:#22d3ee; border:1px solid rgba(34,211,238,.3); padding:10px 20px; }
            .logout-btn,.card-link,.action-btn { border:none; cursor:pointer; padding:10px 20px; }
            .logout-btn,.card-link,.action-btn.primary { background:var(--primary); color:#082f49; }
            .action-btn.secondary { background:rgba(148,163,184,.15); color:var(--text); border:1px solid rgba(148,163,184,.3); }
            .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(320px,1fr)); gap:24px; margin-bottom:40px; }
            .card,.feature-card,.list-item,.empty-state { background:rgba(15,23,42,.78); border:1px solid rgba(148,163,184,.18); border-radius:16px; padding:24px; backdrop-filter:blur(18px); }
            .feature-card { background:rgba(52,211,153,.1); border-color:rgba(52,211,153,.3); }
            .card h2,.feature-card h3,.list-item h3 { color:var(--heading); margin-bottom:12px; }
            .card p,.feature-card p,.list-item p,.empty-state p { color:var(--muted); line-height:1.6; }
            .stat-box { text-align:center; padding:20px; background:rgba(34,211,238,.1); border-radius:12px; margin-bottom:12px; }
            .stat-number { font-size:2rem; color:var(--primary); font-weight:700; }
            .section-title { font-size:1.75rem; color:var(--heading); margin:40px 0 24px; display:flex; align-items:center; gap:10px; }
            .list-item { justify-content:space-between; margin-bottom:12px; }
            .empty-state { text-align:center; }
        </style>
    </head>
    <body>
        <div class="container">
            <header>
                <div>
                    <h1><i class="fa-solid fa-chalkboard-user"></i> Welcome, {{ auth()->user()->name }}</h1>
                    <p style="color: var(--muted); margin-top: 8px;">Trainer Dashboard</p>
                </div>
                <div class="user-info">
                    <a href="{{ route('notifications.index') }}" class="toolbar-link"><i class="fa-regular fa-bell"></i> Notifications</a>
                    <a href="{{ route('profile.show') }}" class="toolbar-link"><i class="fa-regular fa-user"></i> Profile</a>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Log Out</button>
                    </form>
                </div>
            </header>

            <div class="grid">
                <div class="card">
                    <div class="stat-box">
                        <div class="stat-number">{{ count($classes) }}</div>
                        <div>Classes Teaching</div>
                    </div>
                    <a href="{{ route('trainer.classes.index') }}" class="card-link"><i class="fa-solid fa-people-roof"></i> Manage Classes</a>
                </div>
                <div class="card">
                    <h2><i class="fa-regular fa-calendar"></i> Create timetable</h2>
                    <p>Open a class to manage its timetable and structure live learning sessions.</p>
                    <a href="{{ route('trainer.classes.index') }}" class="card-link"><i class="fa-solid fa-calendar-plus"></i> Timetable Center</a>
                </div>
                <div class="card">
                    <h2><i class="fa-solid fa-file-pen"></i> Manage homework</h2>
                    <p>Review submissions, provide marks, and keep each class on schedule.</p>
                    <a href="{{ route('trainer.classes.index') }}" class="card-link"><i class="fa-solid fa-list-check"></i> Homework Center</a>
                </div>
                <div class="card">
                    <h2><i class="fa-solid fa-file-signature"></i> Manage exams</h2>
                    <p>Create exams, accept typed or uploaded answers, and grade submissions.</p>
                    <a href="{{ route('trainer.classes.index') }}" class="card-link"><i class="fa-solid fa-graduation-cap"></i> Exam Center</a>
                </div>
            </div>

            <div class="section-title"><i class="fa-solid fa-sparkles"></i> Trainer Features</div>
            <div class="grid">
                <div class="feature-card">
                    <h3><i class="fa-solid fa-book"></i> Give homework</h3>
                    <p>Create assignments for your classes with due dates and submission types.</p>
                </div>
                <div class="feature-card">
                    <h3><i class="fa-regular fa-calendar-days"></i> Build timetables</h3>
                    <p>Set up class schedules with time slots, topics, and meeting links.</p>
                </div>
                <div class="feature-card">
                    <h3><i class="fa-solid fa-user-check"></i> Mark attendance</h3>
                    <p>Track who was present, absent, or late for each class you teach.</p>
                </div>
                <div class="feature-card">
                    <h3><i class="fa-solid fa-square-pen"></i> Grade submissions</h3>
                    <p>Review student work, provide marks, and notify them when grading is complete.</p>
                </div>
                <div class="feature-card">
                    <h3><i class="fa-solid fa-file-signature"></i> Run exams</h3>
                    <p>Publish exams for each class and allow typed answers or file uploads.</p>
                </div>
            </div>

            <div class="section-title"><i class="fa-solid fa-people-group"></i> Your Classes</div>
            @if(count($classes) > 0)
                @foreach($classes as $class)
                    <div class="list-item">
                        <div>
                            <h3>{{ $class->name }}</h3>
                            <p>{{ $class->description ?? 'No description provided yet.' }}</p>
                            <p style="font-size: 0.85rem; margin-top: 4px; color: #16a34a;">
                                <i class="fa-solid fa-circle-check"></i> {{ ucfirst($class->status) }} · {{ $class->students_count ?? 0 }} students
                            </p>
                        </div>
                        <div class="action-buttons">
                            <a href="{{ route('trainer.classes.show', $class->id) }}" class="action-btn primary"><i class="fa-solid fa-eye"></i> Open</a>
                            <a href="{{ route('trainer.timetable.index', $class->id) }}" class="action-btn secondary"><i class="fa-regular fa-calendar"></i> Timetable</a>
                            <a href="{{ route('trainer.homework.index', $class->id) }}" class="action-btn secondary"><i class="fa-solid fa-file-pen"></i> Homework</a>
                            <a href="{{ route('trainer.exams.index', $class->id) }}" class="action-btn secondary"><i class="fa-solid fa-file-signature"></i> Exams</a>
                            <a href="{{ route('trainer.attendance.index', $class->id) }}" class="action-btn secondary"><i class="fa-solid fa-clipboard-user"></i> Attendance</a>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <h3><i class="fa-regular fa-folder-open"></i> No classes assigned</h3>
                    <p>You are not assigned to teach any classes yet. Please contact your administrator.</p>
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
