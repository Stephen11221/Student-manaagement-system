<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Homework</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body{font-family:"Instrument Sans",sans-serif;background:linear-gradient(135deg,#020617,#0f172a 54%,#111827 100%);color:#e2e8f0;margin:0;min-height:100vh}
        .container{max-width:1200px;margin:0 auto;padding:40px 20px}header{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:24px}
        .hw-list{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px}.hw-card{background:rgba(15,23,42,.78);border:1px solid rgba(148,163,184,.18);border-radius:16px;padding:18px}
        .hw-title{color:#f8fafc;font-weight:700;margin:0 0 8px 0}.hw-info{color:#94a3b8;font-size:.9rem;margin:6px 0}.btn{display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border-radius:8px;text-decoration:none;font-weight:700;border:none;cursor:pointer}.primary{background:#22d3ee;color:#082f49}.secondary{background:rgba(34,211,238,.12);color:#22d3ee;border:1px solid rgba(34,211,238,.3)}.danger{background:#ef4444;color:white}.actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:14px}.section-links{display:flex;gap:10px;flex-wrap:wrap;margin:0 0 20px}
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div>
                <h1 style="margin:0;color:#f8fafc;"><i class="fa-solid fa-file-pen"></i> Assignments</h1>
                <p style="color:#94a3b8;">{{ $class->name }}</p>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('trainer.classes.show', $class->id) }}" class="btn secondary"><i class="fa-solid fa-arrow-left"></i> Class Overview</a>
                <a href="{{ route('trainer.homework.create', $class->id) }}" class="btn primary"><i class="fa-solid fa-file-circle-plus"></i> Create Homework</a>
            </div>
        </header>
        <div class="section-links">
            <a href="{{ route('trainer.classes.show', $class->id) }}" class="btn secondary"><i class="fa-solid fa-diagram-project"></i> Overview</a>
            <a href="{{ route('trainer.classes.index') }}" class="btn secondary"><i class="fa-solid fa-school"></i> Classes</a>
            <a href="{{ route('trainer.timetable.index', $class->id) }}" class="btn secondary"><i class="fa-regular fa-calendar"></i> Timetable</a>
            <a href="{{ route('trainer.exams.index', $class->id) }}" class="btn secondary"><i class="fa-solid fa-file-signature"></i> Exams</a>
            <a href="{{ route('trainer.attendance.index', $class->id) }}" class="btn secondary"><i class="fa-solid fa-clipboard-user"></i> Attendance</a>
        </div>
        @if(session('status'))
            <div class="hw-card" style="margin-bottom:16px;color:#86efac;"><i class="fa-solid fa-circle-check"></i> {{ session('status') }}</div>
        @endif
        <div class="hw-list">
            @forelse($homeworks as $hw)
                <div class="hw-card">
                    <h3 class="hw-title">{{ $hw->title }}</h3>
                    <div class="hw-info">Due: {{ optional($hw->due_date)->format('M d, Y') ?? 'No deadline' }}</div>
                    <div class="hw-info">Type: {{ ucfirst($hw->submission_type === 'upload' ? 'file' : $hw->submission_type) }}</div>
                    <div class="hw-info">Status: {{ ucfirst($hw->status ?? 'published') }}</div>
                    <div class="hw-info">Submissions: {{ $hw->submissions_count }}</div>
                    <div class="actions">
                        <a href="{{ route('trainer.homework.submissions', $hw->id) }}" class="btn primary"><i class="fa-solid fa-eye"></i> Submissions</a>
                        <a href="{{ route('trainer.homework.edit', $hw->id) }}" class="btn secondary"><i class="fa-solid fa-pen"></i> Edit</a>
                        <form method="POST" action="{{ route('trainer.homework.delete', $hw->id) }}" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn danger" onclick="return confirm('Delete this homework item?')"><i class="fa-solid fa-trash"></i> Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <p>No homework assigned yet.</p>
            @endforelse
        </div>
    </div>
</body>
</html>
