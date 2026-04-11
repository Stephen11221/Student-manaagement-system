<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $class->name }} | Student Class</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body{font-family:"Instrument Sans",sans-serif;background:linear-gradient(135deg,#020617,#0f172a 54%,#111827 100%);color:#e2e8f0;margin:0;min-height:100vh}
        .container{max-width:1100px;margin:0 auto;padding:40px 20px}.panel,.card{background:rgba(15,23,42,.78);border:1px solid rgba(148,163,184,.18);border-radius:18px;padding:24px}
        header{display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:flex-start;margin-bottom:24px}.btn{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:8px;text-decoration:none;font-weight:700;border:none;cursor:pointer}
        .primary{background:linear-gradient(135deg,#22d3ee,#06b6d4);color:#082f49}.secondary{background:rgba(34,211,238,.12);color:#22d3ee;border:1px solid rgba(34,211,238,.3)}.danger{background:rgba(239,68,68,.14);color:#fecaca;border:1px solid rgba(239,68,68,.3)}
        .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:16px}.muted{color:#94a3b8}.item{padding:14px;border-radius:12px;background:rgba(2,6,23,.38);border:1px solid rgba(148,163,184,.12);margin-bottom:12px}
        .badge{display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border-radius:999px;font-size:.82rem;font-weight:700}.open{background:rgba(148,163,184,.14);color:#cbd5e1}.submitted{background:rgba(16,185,129,.14);color:#86efac}.graded{background:rgba(250,204,21,.14);color:#fde68a}
    </style>
</head>
<body>
    <div class="container">
        @php
            $joinUrl = studentClassJoinUrl($class);
            $joinLabel = studentClassJoinLabel($class);
            $primaryMeetingLink = $class->timetables->firstWhere('meeting_link')?->meeting_link;
        @endphp
        <header>
            <div>
                <h1 style="margin:0;color:#f8fafc;"><i class="fa-solid fa-school"></i> {{ $class->name }}</h1>
                <p class="muted" style="margin-top:8px;">{{ $class->description ?: 'No class description yet.' }}</p>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                <span class="badge open"><i class="fa-solid fa-circle-info"></i> {{ ucfirst($class->delivery_mode ?? 'physical') }} class</span>
                <a href="{{ $joinUrl }}" class="btn primary">
                    <i class="fa-solid fa-arrow-right"></i> {{ $joinLabel }}
                </a>
                <a href="{{ route('dashboard') }}" class="btn secondary"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
                <form method="POST" action="{{ route('student.classes.unenroll', $class->id) }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn danger" onclick="return confirm('Unenroll from this class?')"><i class="fa-solid fa-user-minus"></i> Unenroll</button>
                </form>
            </div>
        </header>

        <div class="grid">
            <div class="panel" id="location">
                <h2 style="margin-top:0;color:#f8fafc;"><i class="fa-solid fa-user-tie"></i> Trainer</h2>
                <p>{{ $class->trainer?->name ?? 'Not assigned' }}</p>
                <p class="muted">{{ $class->trainer?->email ?? 'No email available' }}</p>
                <p class="muted">{{ $class->delivery_mode === 'online' ? 'Live online class' : ($class->room_number ? 'Room '.$class->room_number : 'Room not set') }}</p>
                <p class="muted">{{ $class->delivery_mode === 'online' ? ($primaryMeetingLink ? 'Tap Join Online Class to open the session.' : 'No meeting link has been added yet.') : 'Use the class room details below to join in person.' }}</p>
            </div>
            <div class="panel" id="schedule">
                <h2 style="margin-top:0;color:#f8fafc;"><i class="fa-regular fa-calendar"></i> Schedule</h2>
                @forelse($class->timetables as $slot)
                    <div class="item">
                        <div style="font-weight:700;color:#f8fafc;">{{ $slot->day_of_week }} • {{ $slot->time_range }}</div>
                        <div class="muted">{{ $slot->topic ?: 'Topic not set' }}</div>
                    </div>
                @empty
                    <p class="muted">No timetable published yet.</p>
                @endforelse
            </div>
        </div>

        <div class="panel" style="margin-top:16px;">
            <h2 style="margin-top:0;color:#f8fafc;"><i class="fa-solid fa-file-pen"></i> Class Homework</h2>
            @forelse($class->homeworks as $homework)
                @php
                    $submission = $homework->submissions->first();
                    $state = $submission?->status === 'graded' ? 'graded' : ($submission ? 'submitted' : 'open');
                @endphp
                <div class="item" style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;">
                    <div>
                        <div style="font-weight:700;color:#f8fafc;">{{ $homework->title }}</div>
                        <div class="muted">Due {{ $homework->due_date ? $homework->due_date->format('M d, Y') : 'No deadline' }} • {{ ucfirst($homework->submission_type === 'upload' ? 'file upload' : $homework->submission_type) }}</div>
                    </div>
                    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                        <span class="badge {{ $state }}">{{ ucfirst($state) }}{{ $submission?->marks !== null ? ' '.$submission->marks.'%' : '' }}</span>
                        <a href="{{ route('student.homework.submit', $homework->id) }}" class="btn primary"><i class="fa-solid fa-arrow-up-right-from-square"></i> Open</a>
                    </div>
                </div>
            @empty
                <p class="muted">No homework yet for this class.</p>
            @endforelse
        </div>

        <div class="panel" style="margin-top:16px;">
            <h2 style="margin-top:0;color:#f8fafc;"><i class="fa-solid fa-file-signature"></i> Class Exams</h2>
            @forelse($class->exams as $exam)
                @php
                    $examSubmission = $exam->submissions->first();
                    $examState = $examSubmission?->status === 'graded' ? 'graded' : ($examSubmission ? 'submitted' : 'open');
                @endphp
                <div class="item" style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;">
                    <div>
                        <div style="font-weight:700;color:#f8fafc;">{{ $exam->title }}</div>
                        <div class="muted">
                            {{ $exam->exam_date ? 'Exam on '.$exam->exam_date->format('M d, Y') : 'No exam date set' }}
                            •
                            {{ ucfirst($exam->submission_type === 'upload' ? 'file upload' : $exam->submission_type) }}
                        </div>
                    </div>
                    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                        <span class="badge {{ $examState }}">{{ ucfirst($examState) }}{{ $examSubmission?->marks !== null ? ' '.$examSubmission->marks.'%' : '' }}</span>
                        <a href="{{ route('student.exams.submit', $exam->id) }}" class="btn primary"><i class="fa-solid fa-arrow-up-right-from-square"></i> Open</a>
                    </div>
                </div>
            @empty
                <p class="muted">No exams yet for this class.</p>
            @endforelse
        </div>
    </div>
</body>
</html>
