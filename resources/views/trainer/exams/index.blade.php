<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exams | {{ config('app.name', 'School Portal') }}</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body{font-family:"Instrument Sans",sans-serif;background:linear-gradient(135deg,#020617,#0f172a 54%,#111827 100%);color:#e2e8f0;margin:0;min-height:100vh}
        .container{max-width:1280px;margin:0 auto;padding:40px 20px 56px}
        header{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:24px}
        .panel,.card,.submission-card,.empty-state{background:rgba(15,23,42,.78);border:1px solid rgba(148,163,184,.18);border-radius:18px}
        .panel,.card,.submission-card,.empty-state{padding:20px}
        .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:10px;text-decoration:none;font-weight:700;border:none;cursor:pointer}
        .primary{background:#22d3ee;color:#082f49}
        .secondary{background:rgba(34,211,238,.12);color:#22d3ee;border:1px solid rgba(34,211,238,.3)}
        .danger{background:#ef4444;color:#fff}
        .muted{color:#94a3b8}
        .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;margin-bottom:22px}
        .stat-number{font-size:1.8rem;font-weight:800;color:#22d3ee}
        .two-col{display:grid;grid-template-columns:1.15fr .85fr;gap:20px}
        .list{display:grid;gap:16px}
        .exam-actions,.submission-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:14px}
        .mini-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 12px;border-radius:8px;border:none;cursor:pointer;text-decoration:none;font-weight:700}
        .mini-btn.primary{background:#22d3ee;color:#082f49}
        .mini-btn.secondary{background:rgba(34,211,238,.12);color:#22d3ee;border:1px solid rgba(34,211,238,.3)}
        .mini-btn.danger{background:rgba(239,68,68,.14);color:#fca5a5;border:1px solid rgba(239,68,68,.3)}
        .section-links{display:flex;gap:10px;flex-wrap:wrap;margin:0 0 20px}
        @media (max-width: 960px){.two-col{grid-template-columns:1fr}}
    </style>
</head>
<body>
    @php
        $submittedCount = $recentSubmissions->whereNotNull('submitted_at')->count();
        $gradedCount = $recentSubmissions->where('status', 'graded')->count();
        $pendingCount = $recentSubmissions->where('status', 'submitted')->count();
    @endphp

    <div class="container">
        <header>
            <div>
                <h1 style="margin:0;color:#f8fafc;"><i class="fa-solid fa-file-signature"></i> Exams</h1>
                <p class="muted" style="margin-top:8px;">{{ $class->name }} • create exams and review submitted exam work in one place.</p>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('trainer.classes.show', $class->id) }}" class="btn secondary"><i class="fa-solid fa-arrow-left"></i> Class Overview</a>
                <a href="{{ route('trainer.exams.create', $class->id) }}" class="btn primary"><i class="fa-solid fa-plus"></i> Create Exam</a>
            </div>
        </header>

        <div class="section-links">
            <a href="{{ route('trainer.classes.show', $class->id) }}" class="btn secondary"><i class="fa-solid fa-diagram-project"></i> Overview</a>
            <a href="{{ route('trainer.classes.index') }}" class="btn secondary"><i class="fa-solid fa-school"></i> Classes</a>
            <a href="{{ route('trainer.timetable.index', $class->id) }}" class="btn secondary"><i class="fa-regular fa-calendar"></i> Timetable</a>
            <a href="{{ route('trainer.homework.index', $class->id) }}" class="btn secondary"><i class="fa-solid fa-file-pen"></i> Homework</a>
            <a href="{{ route('trainer.attendance.index', $class->id) }}" class="btn secondary"><i class="fa-solid fa-clipboard-user"></i> Attendance</a>
        </div>

        @if(session('status'))
            <div class="panel" style="margin-bottom:16px;color:#86efac;"><i class="fa-solid fa-circle-check"></i> {{ session('status') }}</div>
        @endif

        <div class="stats">
            <div class="panel"><div class="stat-number">{{ $exams->count() }}</div><div class="muted">Exams</div></div>
            <div class="panel"><div class="stat-number">{{ $submittedCount }}</div><div class="muted">Submitted</div></div>
            <div class="panel"><div class="stat-number">{{ $pendingCount }}</div><div class="muted">Waiting For Review</div></div>
            <div class="panel"><div class="stat-number">{{ $gradedCount }}</div><div class="muted">Graded</div></div>
        </div>

        <div class="two-col">
            <section class="panel">
                <h2 style="margin:0 0 16px;color:#f8fafc;">Exam Library</h2>
                <div class="list">
                    @forelse($exams as $exam)
                        <article class="card">
                            <h3 style="margin:0 0 8px;color:#f8fafc;">{{ $exam->title }}</h3>
                            <div class="muted">Exam Date: {{ $exam->exam_date ? $exam->exam_date->format('M d, Y') : 'Not set' }}</div>
                            <div class="muted">Type: {{ ucfirst($exam->submission_type === 'upload' ? 'file upload' : $exam->submission_type) }}</div>
                            <div class="muted">Status: {{ ucfirst($exam->status) }}</div>
                            <div class="muted">Submissions: {{ $exam->submissions_count }}</div>
                            <div class="exam-actions">
                                <a href="{{ route('trainer.exams.submissions', $exam->id) }}" class="mini-btn primary"><i class="fa-solid fa-eye"></i> Submitted Exams</a>
                                <a href="{{ route('trainer.exams.edit', $exam->id) }}" class="mini-btn secondary"><i class="fa-solid fa-pen"></i> Edit</a>
                                <form method="POST" action="{{ route('trainer.exams.delete', $exam->id) }}" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="mini-btn danger" onclick="return confirm('Delete this exam?')"><i class="fa-solid fa-trash"></i> Delete</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="empty-state">
                            <h3 style="margin:0 0 8px;color:#f8fafc;">No exams created yet</h3>
                            <p class="muted" style="margin:0;">Create your first exam to start collecting submissions.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="panel">
                <h2 style="margin:0 0 16px;color:#f8fafc;">Recent Submitted Exams</h2>
                <div class="list">
                    @forelse($recentSubmissions as $submission)
                        <article class="submission-card">
                            <div style="font-weight:800;color:#f8fafc;">{{ $submission->student?->name ?? 'Student removed' }}</div>
                            <div class="muted" style="margin-top:6px;">{{ $submission->exam?->title ?? 'Exam unavailable' }}</div>
                            <div class="muted" style="margin-top:6px;">Submitted: {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y H:i') : 'Draft / pending' }}</div>
                            <div class="muted" style="margin-top:6px;">Status: {{ ucfirst($submission->status ?? 'pending') }}</div>
                            <div class="submission-actions">
                                @if($submission->exam)
                                    <a href="{{ route('trainer.exams.submissions', $submission->exam->id) }}" class="mini-btn secondary"><i class="fa-solid fa-list-check"></i> Open Exam</a>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="empty-state">
                            <h3 style="margin:0 0 8px;color:#f8fafc;">No submitted exams yet</h3>
                            <p class="muted" style="margin:0;">Student submissions will appear here as soon as learners submit their exam work.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</body>
</html>
