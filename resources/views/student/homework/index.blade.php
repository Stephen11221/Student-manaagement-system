<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Homework | {{ config('app.name', 'School Portal') }}</title>
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
            display:grid;
            gap:20px;
            grid-template-columns:minmax(0,1fr) auto;
            align-items:end;
            padding-bottom:24px;
            border-bottom:1px solid var(--border);
            margin-bottom:24px;
        }
        .eyebrow {
            display:inline-flex; align-items:center; gap:8px;
            border-radius:999px; border:1px solid rgba(56,189,248,.28);
            background:rgba(56,189,248,.12); color:#cffafe;
            padding:8px 12px; font-size:.78rem; font-weight:800;
            letter-spacing:.18em; text-transform:uppercase;
        }
        h1,p { margin:0; }
        .page-title { margin-top:12px; font-size:clamp(2rem,3.4vw,3rem); line-height:1.05; font-weight:800; }
        .page-copy { margin-top:12px; max-width:72ch; color:var(--muted); line-height:1.7; }
        .btn {
            display:inline-flex; align-items:center; justify-content:center; gap:8px;
            border-radius:16px; padding:12px 16px; border:1px solid transparent;
            text-decoration:none; font-weight:800; transition:transform 160ms ease, background 160ms ease, border-color 160ms ease;
        }
        .btn:hover { transform:translateY(-1px); }
        .btn-primary { background:linear-gradient(135deg,var(--primary),var(--primary-strong)); color:#082f49; }
        .btn-secondary { background:rgba(15,23,42,.92); color:var(--text); border-color:var(--border); }
        .surface {
            background:var(--surface); border:1px solid var(--border); border-radius:28px;
            box-shadow:0 24px 80px rgba(2,6,23,.34); backdrop-filter:blur(18px);
        }
        .surface-pad { padding:24px; }
        .stats {
            display:grid; gap:16px; grid-template-columns:repeat(4, minmax(0, 1fr));
            margin-top:24px;
        }
        .stat-card {
            background:var(--surface-strong); border:1px solid var(--border); border-radius:24px; padding:18px;
        }
        .stat-label { display:inline-flex; align-items:center; gap:8px; color:var(--muted); font-size:.8rem; font-weight:800; text-transform:uppercase; letter-spacing:.12em; }
        .stat-value { margin-top:10px; font-size:2rem; font-weight:800; color:var(--text); }
        .stat-value.success { color:#86efac; }
        .stat-value.warning { color:#fcd34d; }
        .stat-value.danger { color:#fda4af; }
        .grid { display:grid; gap:16px; }
        .homework-item {
            display:grid; grid-template-columns:minmax(0,1fr) auto; gap:18px;
            padding:22px; background:var(--surface-strong); border:1px solid var(--border); border-radius:24px;
        }
        .hw-title { font-size:1.2rem; font-weight:800; color:var(--text); margin-bottom:8px; }
        .hw-desc { color:var(--muted); font-size:.95rem; line-height:1.6; }
        .meta-row { display:flex; flex-wrap:wrap; gap:10px; margin-top:14px; }
        .chip {
            display:inline-flex; align-items:center; gap:8px; border-radius:999px; padding:8px 12px;
            font-size:.82rem; font-weight:800; border:1px solid transparent;
        }
        .chip-class { background:rgba(59,130,246,.14); color:#bfdbfe; border-color:rgba(59,130,246,.28); }
        .chip-due { background:rgba(245,158,11,.14); color:#fde68a; border-color:rgba(245,158,11,.28); }
        .chip-open { background:rgba(148,163,184,.14); color:#e2e8f0; border-color:rgba(148,163,184,.24); }
        .chip-submitted { background:rgba(34,197,94,.14); color:#bbf7d0; border-color:rgba(34,197,94,.28); }
        .chip-graded { background:rgba(250,204,21,.14); color:#fef08a; border-color:rgba(250,204,21,.28); }
        .chip-late { background:rgba(239,68,68,.14); color:#fecaca; border-color:rgba(239,68,68,.28); }
        .action-group { display:grid; gap:10px; justify-items:end; align-content:start; min-width:200px; }
        .action-note { color:var(--muted); font-size:.82rem; text-align:right; line-height:1.5; }
        .alert {
            margin-top:24px; border-radius:24px; border:1px solid rgba(6,182,212,.28); background:rgba(6,182,212,.1); padding:18px;
        }
        .empty-state {
            margin-top:24px; padding:32px; text-align:center;
            background:var(--surface-strong); border:1px solid var(--border); border-radius:24px; color:var(--muted);
        }
        @media (max-width: 1000px) {
            .page-header, .homework-item { grid-template-columns: 1fr; }
            .action-group { justify-items:start; }
            .action-note { text-align:left; }
            .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 640px) {
            .page-shell { width:calc(100% - 24px); padding-top:24px; }
            .stats { grid-template-columns:1fr; }
            .surface-pad { padding:18px; }
            .btn { width:100%; }
        }
    </style>
</head>
<body>
    <div class="page-shell">
        <header class="page-header">
            <div>
                <p class="eyebrow"><i class="fa-solid fa-book"></i> Homework</p>
                <h1 class="page-title">Assignments</h1>
                <p class="page-copy">See every assignment in a scannable card layout. Deadlines, status, and the submit action are all visible immediately so students know what to do next.</p>
            </div>
            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
                @endauth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-primary" type="submit"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
                </form>
            </div>
        </header>

        <section class="surface surface-pad">
            <div class="section-title" style="display:flex; align-items:center; gap:10px; font-size:1.05rem; font-weight:800;">
                <i class="fa-solid fa-circle-info"></i> Assignment overview
            </div>
            <p class="page-copy" style="margin-top:8px;">Keep an eye on due dates and submission status. Late work is shown in red, submitted work in green, and open tasks in neutral tones for fast scanning.</p>

            <div class="stats">
                <div class="stat-card">
                    <div class="stat-label"><i class="fa-solid fa-file-pen"></i> Total tasks</div>
                    <div class="stat-value">{{ $homeworks->count() }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label"><i class="fa-solid fa-paper-plane"></i> Submitted</div>
                    <div class="stat-value success">{{ $homeworks->filter(fn ($hw) => optional($hw->submissions->first())->status === 'submitted' || optional($hw->submissions->first())->status === 'graded')->count() }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label"><i class="fa-solid fa-clock"></i> Open</div>
                    <div class="stat-value warning">{{ $homeworks->filter(fn ($hw) => ! $hw->due_date || $hw->due_date->isFuture())->count() }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label"><i class="fa-solid fa-triangle-exclamation"></i> Needs attention</div>
                    <div class="stat-value danger">{{ $homeworks->filter(fn ($hw) => $hw->due_date && $hw->due_date->isPast() && ! in_array(optional($hw->submissions->first())->status, ['submitted','graded'], true))->count() }}</div>
                </div>
            </div>
        </section>

        @if (session('status'))
            <div class="alert">
                <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
            </div>
        @endif

        @if ($homeworks->count() > 0)
            <div class="grid" style="margin-top:24px;">
                @foreach ($homeworks as $hw)
                    @php
                        $submission = $hw->submissions->first();
                        $isGraded = $submission && $submission->status === 'graded';
                        $isSubmitted = $submission && in_array($submission->status, ['submitted', 'graded'], true);
                        $isLate = $hw->due_date && $hw->due_date->isPast() && ! $isSubmitted;
                    @endphp
                    <article class="homework-item">
                        <div>
                            <h3 class="hw-title"><i class="fa-solid fa-file-alt"></i> {{ $hw->title }}</h3>
                            <p class="hw-desc">{{ $hw->description ?: 'No assignment instructions were added yet.' }}</p>
                            <div class="meta-row">
                                <span class="chip chip-class"><i class="fa-solid fa-school"></i> {{ $hw->class?->name ?? 'Class unavailable' }}</span>
                                <span class="chip chip-due"><i class="fa-regular fa-calendar"></i> {{ $hw->due_date ? 'Due '.$hw->due_date->format('M d, Y') : 'No deadline' }}</span>
                                @if ($isGraded)
                                    <span class="chip chip-graded"><i class="fa-solid fa-star"></i> Graded{{ $submission->marks !== null ? ': '.$submission->marks.'%' : '' }}</span>
                                @elseif ($isSubmitted)
                                    <span class="chip chip-submitted"><i class="fa-solid fa-circle-check"></i> Submitted</span>
                                @elseif ($isLate)
                                    <span class="chip chip-late"><i class="fa-solid fa-triangle-exclamation"></i> Late</span>
                                @else
                                    <span class="chip chip-open"><i class="fa-regular fa-clock"></i> Awaiting submission</span>
                                @endif
                            </div>
                        </div>

                        <div class="action-group">
                            <a href="{{ route('student.homework.submit', $hw->id) }}" class="btn btn-primary" style="width:100%;">
                                <i class="fa-solid fa-file-arrow-up"></i>
                                {{ $isSubmitted ? 'Review Submission' : 'Submit Work' }}
                            </a>
                            <div class="action-note">
                                {{ ucfirst($hw->submission_type === 'upload' ? 'File upload' : $hw->submission_type) }}
                                @if ($hw->class?->trainer)
                                    with {{ $hw->class->trainer->name }}
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div style="font-size: 3rem; color: var(--primary); margin-bottom: 12px;">
                    <i class="fa-regular fa-folder-open"></i>
                </div>
                <div style="font-size:1.25rem; font-weight:800; color: var(--text); margin-bottom:8px;">No assignments yet</div>
                <div>New homework will appear here once your trainers publish it.</div>
            </div>
        @endif
    </div>
</body>
</html>
