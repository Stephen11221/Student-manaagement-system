<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $class->name }} | Trainer Class</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body{font-family:"Instrument Sans",sans-serif;background:linear-gradient(135deg,#020617,#0f172a 54%,#111827 100%);color:#e2e8f0;margin:0;min-height:100vh}
        .container{max-width:1200px;margin:0 auto;padding:40px 20px}.panel,.card{background:rgba(15,23,42,.78);border:1px solid rgba(148,163,184,.18);border-radius:18px;padding:24px}
        header{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;margin-bottom:24px}.btn{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:8px;text-decoration:none;font-weight:700;border:none;cursor:pointer}
        .btn-primary{background:linear-gradient(135deg,#22d3ee,#06b6d4);color:#082f49}.btn-secondary{background:rgba(34,211,238,.12);color:#22d3ee;border:1px solid rgba(34,211,238,.3)}
        .stats,.grid{display:grid;gap:16px}.stats{grid-template-columns:repeat(auto-fit,minmax(180px,1fr));margin-bottom:24px}.grid{grid-template-columns:repeat(auto-fit,minmax(320px,1fr))}
        .muted{color:#94a3b8}.list{display:grid;gap:12px}.item{padding:14px;border-radius:12px;background:rgba(2,6,23,.38);border:1px solid rgba(148,163,184,.12)}
        .nav-card{display:flex;flex-direction:column;gap:14px}
        .section-links{display:flex;gap:10px;flex-wrap:wrap;margin:0 0 20px}
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div>
                <h1 style="margin:0;color:#f8fafc;"><i class="fa-solid fa-school"></i> {{ $class->name }}</h1>
                <p class="muted" style="margin-top:8px;">{{ $class->description ?: 'No class description yet.' }}</p>
                <p class="muted" style="margin-top:8px;"><i class="fa-solid fa-circle-info"></i> {{ ucfirst($class->delivery_mode ?? 'physical') }} class{{ $class->delivery_mode === 'online' ? ' with live join links' : '' }}</p>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('trainer.classes.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back to Classes</a>
                <a href="{{ route('trainer.timetable.create', $class->id) }}" class="btn btn-primary"><i class="fa-regular fa-calendar-plus"></i> Add Timetable</a>
                <a href="{{ route('trainer.homework.create', $class->id) }}" class="btn btn-primary"><i class="fa-solid fa-file-circle-plus"></i> Add Homework</a>
                <a href="{{ route('trainer.exams.create', $class->id) }}" class="btn btn-primary"><i class="fa-solid fa-file-signature"></i> Add Exam</a>
                <a href="{{ route('trainer.attendance.index', $class->id) }}" class="btn btn-secondary"><i class="fa-solid fa-clipboard-user"></i> Mark Attendance</a>
            </div>
        </header>

        <div class="section-links">
            <a href="{{ route('trainer.classes.index') }}" class="btn btn-secondary"><i class="fa-solid fa-school"></i> Classes</a>
            <a href="{{ route('trainer.timetable.index', $class->id) }}" class="btn btn-secondary"><i class="fa-regular fa-calendar"></i> Timetable</a>
            <a href="{{ route('trainer.homework.index', $class->id) }}" class="btn btn-secondary"><i class="fa-solid fa-file-pen"></i> Homework</a>
            <a href="{{ route('trainer.exams.index', $class->id) }}" class="btn btn-secondary"><i class="fa-solid fa-file-signature"></i> Exams</a>
            <a href="{{ route('trainer.attendance.index', $class->id) }}" class="btn btn-secondary"><i class="fa-solid fa-clipboard-user"></i> Attendance</a>
        </div>

        <div class="stats">
            <div class="panel"><div class="muted">Students</div><div style="font-size:2rem;color:#22d3ee;font-weight:700;">{{ $class->students_count }}</div></div>
            <div class="panel"><div class="muted">Timetable Slots</div><div style="font-size:2rem;color:#22d3ee;font-weight:700;">{{ $class->timetables->count() }}</div></div>
            <div class="panel"><div class="muted">Homework Items</div><div style="font-size:2rem;color:#22d3ee;font-weight:700;">{{ $class->homeworks->count() }}</div></div>
            <div class="panel"><div class="muted">Exams</div><div style="font-size:2rem;color:#22d3ee;font-weight:700;">{{ $class->exams->count() }}</div></div>
        </div>

        <div class="grid">
            <div class="card">
                <h2 style="margin-top:0;color:#f8fafc;"><i class="fa-solid fa-diagram-project"></i> Class Sections</h2>
                <div class="list">
                    <div class="item nav-card">
                        <div>
                            <div style="font-weight:700;color:#f8fafc;">Class Details</div>
                            <div class="muted">Stay here for roster, summary stats, and class information.</div>
                        </div>
                        <a href="{{ route('trainer.classes.edit', $class->id) }}" class="btn btn-secondary"><i class="fa-solid fa-pen"></i> Edit Class</a>
                    </div>
                    <div class="item nav-card">
                        <div>
                            <div style="font-weight:700;color:#f8fafc;">Timetable</div>
                            <div class="muted">Manage the full class schedule in its own page.</div>
                        </div>
                        <a href="{{ route('trainer.timetable.index', $class->id) }}" class="btn btn-secondary"><i class="fa-regular fa-calendar"></i> Open Timetable</a>
                    </div>
                    <div class="item nav-card">
                        <div>
                            <div style="font-weight:700;color:#f8fafc;">Homework</div>
                            <div class="muted">Create homework and review submissions in a dedicated blade.</div>
                        </div>
                        <a href="{{ route('trainer.homework.index', $class->id) }}" class="btn btn-secondary"><i class="fa-solid fa-file-pen"></i> Open Homework</a>
                    </div>
                    <div class="item nav-card">
                        <div>
                            <div style="font-weight:700;color:#f8fafc;">Exams</div>
                            <div class="muted">Handle exams separately from class details.</div>
                        </div>
                        <a href="{{ route('trainer.exams.index', $class->id) }}" class="btn btn-secondary"><i class="fa-solid fa-file-signature"></i> Open Exams</a>
                    </div>
                </div>
            </div>

            <div class="card">
                <h2 style="margin-top:0;color:#f8fafc;"><i class="fa-solid fa-users"></i> Student Roster</h2>
                <div class="list">
                    @forelse($class->students as $student)
                        <div class="item">
                            <div style="font-weight:700;color:#f8fafc;">{{ $student->name }}</div>
                            <div class="muted">{{ $student->email }}</div>
                        </div>
                    @empty
                        <p class="muted">No students enrolled yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</body>
</html>
