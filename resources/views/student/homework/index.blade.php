<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Homework</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family:"Instrument Sans",sans-serif; background:linear-gradient(135deg,#020617,#0f172a 54%,#111827 100%); color:#e2e8f0; margin:0; min-height:100vh; }
        .container { max-width:1200px; margin:0 auto; padding:40px 20px; }
        header { display:flex; justify-content:space-between; align-items:center; margin-bottom:32px; border-bottom:1px solid rgba(148,163,184,.1); padding-bottom:20px; gap:12px; flex-wrap:wrap; }
        h1 { font-size:2rem; color:#f8fafc; margin:0; }
        .toolbar { display:flex; gap:8px; flex-wrap:wrap; }
        .btn { background:linear-gradient(135deg,#22d3ee,#06b6d4); color:#082f49; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
        .btn-muted { background:rgba(34,211,238,.12); color:#22d3ee; border:1px solid rgba(34,211,238,.3); }
        .status-message { margin-bottom:20px; background:rgba(16,185,129,.15); border:1px solid rgba(16,185,129,.3); color:#6ee7b7; padding:14px 16px; border-radius:12px; }
        .grid { display:grid; gap:16px; }
        .homework-item { background:rgba(15,23,42,.78); border:1px solid rgba(148,163,184,.18); border-radius:16px; padding:22px; display:flex; justify-content:space-between; align-items:flex-start; gap:18px; }
        .hw-content { flex:1; }
        .hw-content h3 { color:#f8fafc; margin:0 0 8px; }
        .hw-content p { color:#94a3b8; font-size:.95rem; margin:6px 0; line-height:1.5; }
        .meta-row { display:flex; flex-wrap:wrap; gap:10px; margin-top:14px; }
        .pill { display:inline-flex; align-items:center; gap:8px; border-radius:999px; padding:8px 12px; font-size:.82rem; font-weight:700; }
        .pill-class { background:rgba(59,130,246,.14); color:#bfdbfe; }
        .pill-due { background:rgba(251,113,133,.14); color:#fda4af; }
        .pill-open { background:rgba(148,163,184,.14); color:#cbd5e1; }
        .pill-submitted { background:rgba(16,185,129,.14); color:#86efac; }
        .pill-graded { background:rgba(250,204,21,.14); color:#fde68a; }
        .action-group { display:flex; flex-direction:column; align-items:flex-end; gap:10px; min-width:190px; }
        .action-note { color:#94a3b8; font-size:.82rem; text-align:right; }
        .empty-state { background:rgba(15,23,42,.78); border:1px solid rgba(148,163,184,.18); border-radius:16px; padding:32px; text-align:center; color:#94a3b8; }
        @media (max-width: 768px) {
            .homework-item { flex-direction:column; }
            .action-group { align-items:stretch; min-width:0; width:100%; }
            .action-note { text-align:left; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fa-solid fa-book-open"></i> Assignments</h1>
            <div class="toolbar">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-muted"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
                @endauth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn" type="submit"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
                </form>
            </div>
        </header>

        @if (session('status'))
            <div class="status-message">
                <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
            </div>
        @endif

        @if ($homeworks->count() > 0)
            <div class="grid">
                @foreach ($homeworks as $hw)
                    @php
                        $submission = $hw->submissions->first();
                        $isGraded = $submission && $submission->status === 'graded';
                        $isSubmitted = $submission && in_array($submission->status, ['submitted', 'graded'], true);
                    @endphp
                    <div class="homework-item">
                        <div class="hw-content">
                            <h3>{{ $hw->title }}</h3>
                            <p>{{ $hw->description ?: 'No assignment instructions were added yet.' }}</p>
                            <div class="meta-row">
                                <span class="pill pill-class"><i class="fa-solid fa-school"></i> {{ $hw->class?->name ?? 'Class unavailable' }}</span>
                                <span class="pill pill-due"><i class="fa-regular fa-calendar"></i> {{ $hw->due_date ? 'Due '.$hw->due_date->format('M d, Y') : 'No deadline' }}</span>
                                @if ($isGraded)
                                    <span class="pill pill-graded"><i class="fa-solid fa-star"></i> Graded{{ $submission->marks !== null ? ': '.$submission->marks.'%' : '' }}</span>
                                @elseif ($isSubmitted)
                                    <span class="pill pill-submitted"><i class="fa-solid fa-paper-plane"></i> Submitted</span>
                                @else
                                    <span class="pill pill-open"><i class="fa-regular fa-clock"></i> Awaiting submission</span>
                                @endif
                            </div>
                        </div>
                        <div class="action-group">
                            <a href="{{ route('student.homework.submit', $hw->id) }}" class="btn">
                                <i class="fa-solid fa-file-pen"></i>
                                {{ $isSubmitted ? 'Review Submission' : 'Submit Work' }}
                            </a>
                            <div class="action-note">
                                {{ ucfirst($hw->submission_type === 'upload' ? 'file upload' : $hw->submission_type) }}
                                @if ($hw->class?->trainer)
                                    with {{ $hw->class->trainer->name }}
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <p><i class="fa-regular fa-folder-open"></i> No assignments yet. New homework will appear here once your trainers publish it.</p>
            </div>
        @endif
    </div>
</body>
</html>
