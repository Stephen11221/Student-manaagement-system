<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Student Dashboard | {{ config('app.name', 'School Portal') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            :root { color-scheme: dark; --text:#e2e8f0; --muted:#94a3b8; --heading:#f8fafc; --primary:#22d3ee; }
            * { box-sizing:border-box; margin:0; padding:0; }
            body { font-family:"Instrument Sans",sans-serif; color:var(--text); background:linear-gradient(135deg,#020617 0%,#0f172a 54%,#111827 100%); min-height:100vh; }
            .container { max-width:1400px; margin:0 auto; padding:40px 20px; }
            header,.user-info,.list-item { display:flex; align-items:center; }
            header { justify-content:space-between; margin-bottom:40px; padding-bottom:20px; border-bottom:1px solid rgba(148,163,184,.1); gap:16px; flex-wrap:wrap; }
            h1 { font-size:2.5rem; color:var(--heading); }
            .user-info { gap:10px; flex-wrap:wrap; }
            .toolbar-link,.logout-btn,.card-link,.action-btn { display:inline-flex; align-items:center; gap:8px; border-radius:8px; font-weight:700; text-decoration:none; transition:.2s; }
            .toolbar-link { background:rgba(34,211,238,.1); color:#22d3ee; border:1px solid rgba(34,211,238,.3); padding:10px 20px; }
            .logout-btn,.card-link,.action-btn { background:var(--primary); color:#082f49; border:none; padding:10px 20px; }
            .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(320px,1fr)); gap:24px; margin-bottom:40px; }
            .card,.list-item,.empty-state,.status-banner { background:rgba(15,23,42,.78); border:1px solid rgba(148,163,184,.18); border-radius:16px; padding:24px; backdrop-filter:blur(18px); }
            .card h2,.list-item h3,.empty-state h3 { color:var(--heading); margin-bottom:14px; }
            .card p,.list-item p,.empty-state p,.section-title { color:var(--muted); }
            .stat-box { text-align:center; padding:20px; background:rgba(34,211,238,.1); border-radius:12px; margin-bottom:12px; }
            .stat-number { font-size:2rem; color:var(--primary); font-weight:700; }
            .section-title { font-size:1.75rem; margin:40px 0 24px; display:flex; align-items:center; gap:10px; }
            .list-item { justify-content:space-between; margin-bottom:12px; }
            .action-btn { padding:10px 16px; }
            .secondary-btn { background:rgba(34,211,238,.12); color:#22d3ee; border:1px solid rgba(34,211,238,.3); }
            .empty-state { text-align:center; }
            .empty-state-icon { font-size:3rem; margin-bottom:16px; color:var(--primary); }
            .status-banner { margin-bottom:24px; padding:18px 24px; border-color:rgba(34,197,94,.28); background:rgba(22,163,74,.12); color:#bbf7d0; }
            .enroll-form { margin:0; }
            .class-meta { display:flex; gap:16px; flex-wrap:wrap; margin-top:8px; }
            .class-meta span { color:var(--muted); font-size:.92rem; }
            .main-content { min-width:0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="app-shell">
                @include('partials.role-sidebar', ['role' => 'student'])

                <main class="app-main main-content">
                    <header>
                        <div>
                            <h1><i class="fa-solid fa-user-graduate"></i> Welcome, {{ auth()->user()->name }}</h1>
                            <p style="color: var(--muted); margin-top: 8px;">Student Dashboard</p>
                        </div>
                    </header>

                    @if (session('status'))
                        <div class="status-banner">
                            <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
                        </div>
                    @endif

                    <div class="grid">
                        @php $currentClass = $classes[0] ?? null; @endphp
                        <div class="card">
                            <h2><i class="fa-solid fa-right-to-bracket"></i> Time clock</h2>
                            <p>Check in or check out for your current class.</p>
                            <div class="grid" style="grid-template-columns:1fr 1fr; gap:12px; margin-top:16px;">
                                <form method="POST" action="{{ route('attendance.check-in') }}">
                                    @csrf
                                    <input type="hidden" name="scope_type" value="{{ $currentClass ? 'class' : 'global' }}">
                                    <input type="hidden" name="scope_id" value="{{ $currentClass?->id ?? 0 }}">
                                    <button type="submit" class="card-link" style="width:100%; justify-content:center;"><i class="fa-solid fa-arrow-right-to-bracket"></i> Check In</button>
                                </form>
                                <form method="POST" action="{{ route('attendance.check-out') }}">
                                    @csrf
                                    <input type="hidden" name="scope_type" value="{{ $currentClass ? 'class' : 'global' }}">
                                    <input type="hidden" name="scope_id" value="{{ $currentClass?->id ?? 0 }}">
                                    <button type="submit" class="card-link" style="width:100%; justify-content:center;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Check Out</button>
                                </form>
                            </div>
                        </div>
                        <div class="card">
                            <div class="stat-box">
                                <div class="stat-number">{{ count($classes) }}</div>
                                <div>Classes Enrolled</div>
                            </div>
                            <a href="{{ route('student.timetable.index') }}" class="card-link"><i class="fa-regular fa-calendar"></i> View Timetable</a>
                        </div>
                        <div class="card">
                            <h2><i class="fa-solid fa-book-open"></i> Homework</h2>
                            <p>Complete assignments, submit work, and track upcoming deadlines.</p>
                            <a href="{{ route('student.homework.index') }}" class="card-link"><i class="fa-solid fa-file-pen"></i> Go to Homework</a>
                        </div>
                        <div class="card">
                            <h2><i class="fa-solid fa-calendar-days"></i> Attendance</h2>
                            <p>Review your attendance history and stay on top of your class participation.</p>
                            <a href="{{ route('student.attendance.index') }}" class="card-link"><i class="fa-solid fa-clipboard-check"></i> View Attendance</a>
                        </div>
                        <div class="card">
                            <h2><i class="fa-solid fa-file-signature"></i> Exams</h2>
                            <p>Open exam papers, type your answers, or upload your completed exam files.</p>
                            <a href="{{ route('student.exams.index') }}" class="card-link"><i class="fa-solid fa-graduation-cap"></i> Go to Exams</a>
                        </div>
                    </div>

                    <div class="section-title" id="your-classes"><i class="fa-solid fa-school"></i> Your Classes</div>
                    @if(count($classes) > 0)
                        @foreach($classes as $class)
                            <div class="list-item">
                                <div>
                                    <h3>{{ $class->name }}</h3>
                                    <div class="class-meta">
                                        <span><i class="fa-solid fa-chalkboard-user"></i> Trainer: <strong>{{ $class->trainer?->name ?? 'Not assigned' }}</strong></span>
                                        <span><i class="fa-solid fa-location-dot"></i> {{ $class->room_number ? 'Room '.$class->room_number : 'Room not set' }}</span>
                                        <span><i class="fa-solid fa-circle-info"></i> {{ ucfirst($class->delivery_mode ?? 'physical') }} class</span>
                                    </div>
                                    <p style="font-size: 0.85rem; margin-top: 8px; color: #16a34a;"><i class="fa-solid fa-circle-check"></i> {{ ucfirst($class->status) }}</p>
                                </div>
                                <a href="{{ studentClassJoinUrl($class) }}" class="action-btn"><i class="fa-solid fa-arrow-right"></i> {{ studentClassJoinLabel($class) }}</a>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="fa-regular fa-folder-open"></i></div>
                            <h3>No classes enrolled</h3>
                            <p>You are not enrolled in any classes yet. Use the available classes section below to join one.</p>
                        </div>
                    @endif

                    <div class="section-title" id="available-classes"><i class="fa-solid fa-plus"></i> Available Classes</div>
                    @if($classes->count() > 0)
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="fa-solid fa-lock"></i></div>
                            <h3>Your class is locked</h3>
                            <p>You are currently enrolled in {{ $classes->first()->name }}. Other classes are hidden until you unenroll from this one.</p>
                        </div>
                    @elseif($availableClasses->count() > 0)
                        @foreach($availableClasses as $class)
                            <div class="list-item">
                                <div>
                                    <h3>{{ $class->name }}</h3>
                                    <div class="class-meta">
                                        <span><i class="fa-solid fa-chalkboard-user"></i> Trainer: <strong>{{ $class->trainer?->name ?? 'Not assigned' }}</strong></span>
                                        <span><i class="fa-solid fa-location-dot"></i> {{ $class->room_number ? 'Room '.$class->room_number : 'Room not set' }}</span>
                                    </div>
                                    @if($class->description)
                                        <p style="margin-top: 8px;">{{ $class->description }}</p>
                                    @endif
                                </div>
                                <form method="POST" action="{{ route('student.classes.enroll', $class->id) }}" class="enroll-form">
                                    @csrf
                                    <button type="submit" class="action-btn secondary-btn"><i class="fa-solid fa-user-plus"></i> Enroll Class</button>
                                </form>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="fa-regular fa-circle-check"></i></div>
                            <h3>No more classes to join</h3>
                            <p>You are already enrolled in every active class that is currently available.</p>
                        </div>
                    @endif
                </main>
            </div>
        </div>
    
    @include('partials.chat-fab')
    @include('partials.idle-timeout-modal')
    <script src="{{ asset('js/idle-timeout.js') }}"></script>
    <script>
        document.documentElement.dataset.idleTimeout = "{{ config('idle.idle_timeout', 15) }}";
        document.documentElement.dataset.warningTime = "{{ config('idle.warning_time', 1) }}";
    </script>
    </body>
</html>
