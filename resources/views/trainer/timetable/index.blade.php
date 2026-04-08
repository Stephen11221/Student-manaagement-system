<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Timetable | {{ config('app.name', 'School Portal') }}</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body{font-family:"Instrument Sans",sans-serif;background:linear-gradient(135deg,#020617,#0f172a 54%,#111827 100%);color:#e2e8f0;margin:0;min-height:100vh}
        .container{max-width:1280px;margin:0 auto;padding:40px 20px 56px}
        header{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:24px}
        .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:10px;text-decoration:none;font-weight:700;border:none;cursor:pointer}
        .primary{background:linear-gradient(135deg,#22d3ee,#06b6d4);color:#082f49}
        .secondary{background:rgba(34,211,238,.12);color:#22d3ee;border:1px solid rgba(34,211,238,.3)}
        .danger{background:#ef4444;color:#fff}
        .muted{color:#94a3b8}
        .panel,.slot-card,.day-card,.empty-state{background:rgba(15,23,42,.78);border:1px solid rgba(148,163,184,.18);border-radius:18px}
        .panel{padding:20px}
        .banner{margin-bottom:16px;color:#86efac}
        .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;margin-bottom:22px}
        .stat{padding:18px;text-align:center}
        .stat-number{font-size:1.8rem;font-weight:800;color:#22d3ee}
        .toolbar{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:18px}
        .view-switch{display:flex;gap:10px;flex-wrap:wrap}
        .switch-btn{padding:10px 14px;border-radius:999px;border:1px solid rgba(148,163,184,.24);background:rgba(2,6,23,.4);color:#cbd5e1;font-weight:700;cursor:pointer}
        .switch-btn.active{background:rgba(34,211,238,.18);border-color:rgba(34,211,238,.38);color:#22d3ee}
        .view{display:none}
        .view.active{display:block}
        .week-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:16px}
        .day-card{padding:18px}
        .day-title{font-weight:800;color:#f8fafc;margin-bottom:12px}
        .slot-card{padding:14px;margin-bottom:12px}
        .slot-card:last-child{margin-bottom:0}
        .slot-time{font-weight:800;color:#22d3ee}
        .slot-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:12px}
        .mini-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 12px;border-radius:8px;border:none;cursor:pointer;text-decoration:none;font-weight:700}
        .mini-btn.secondary{background:rgba(34,211,238,.12);color:#22d3ee;border:1px solid rgba(34,211,238,.3)}
        .mini-btn.danger{background:rgba(239,68,68,.14);color:#fca5a5;border:1px solid rgba(239,68,68,.3)}
        .month-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:16px}
        .month-cell{padding:18px}
        .month-cell h3{margin:0 0 8px;color:#f8fafc}
        .month-count{font-size:2rem;font-weight:800;color:#22d3ee}
        .empty-state{padding:40px 24px;text-align:center}
        .section-links{display:flex;gap:10px;flex-wrap:wrap;margin:0 0 20px}
    </style>
</head>
<body>
    @php
        $dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $weekView = collect($dayOrder)->mapWithKeys(function ($day) use ($timetables) {
            return [$day => $timetables->where('day_of_week', $day)->sortBy('start_time')->values()];
        });
        $monthSummary = $weekView->map(fn ($slots) => [
            'count' => $slots->count(),
            'topics' => $slots->pluck('topic')->filter()->take(2)->values(),
        ]);
    @endphp

    <div class="container">
        <header>
            <div>
                <h1 style="margin:0;color:#f8fafc;"><i class="fa-regular fa-calendar"></i> Class Timetable</h1>
                <p class="muted" style="margin-top:8px;">{{ $class->name }} • organize the class in weekly and monthly views.</p>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('trainer.classes.show', $class->id) }}" class="btn secondary"><i class="fa-solid fa-arrow-left"></i> Class Overview</a>
                <a href="{{ route('trainer.timetable.create', $class->id) }}" class="btn primary"><i class="fa-regular fa-calendar-plus"></i> Add Slot</a>
            </div>
        </header>

        <div class="section-links">
            <a href="{{ route('trainer.classes.show', $class->id) }}" class="btn secondary"><i class="fa-solid fa-diagram-project"></i> Overview</a>
            <a href="{{ route('trainer.classes.index') }}" class="btn secondary"><i class="fa-solid fa-school"></i> Classes</a>
            <a href="{{ route('trainer.homework.index', $class->id) }}" class="btn secondary"><i class="fa-solid fa-file-pen"></i> Homework</a>
            <a href="{{ route('trainer.exams.index', $class->id) }}" class="btn secondary"><i class="fa-solid fa-file-signature"></i> Exams</a>
            <a href="{{ route('trainer.attendance.index', $class->id) }}" class="btn secondary"><i class="fa-solid fa-clipboard-user"></i> Attendance</a>
        </div>

        @if(session('status'))
            <div class="panel banner"><i class="fa-solid fa-circle-check"></i> {{ session('status') }}</div>
        @endif

        <div class="stats">
            <div class="panel stat"><div class="stat-number">{{ $timetables->count() }}</div><div class="muted">Total Slots</div></div>
            <div class="panel stat"><div class="stat-number">{{ $weekView->filter(fn ($slots) => $slots->isNotEmpty())->count() }}</div><div class="muted">Active Weekdays</div></div>
            <div class="panel stat"><div class="stat-number">{{ $timetables->pluck('meeting_link')->filter()->count() }}</div><div class="muted">Linked Sessions</div></div>
            <div class="panel stat"><div class="stat-number">{{ $timetables->pluck('topic')->filter()->count() }}</div><div class="muted">Slots With Topics</div></div>
        </div>

        <div class="panel">
            <div class="toolbar">
                <div>
                    <h2 style="margin:0;color:#f8fafc;">Schedule Views</h2>
                    <p class="muted" style="margin:6px 0 0;">Switch between a weekly teaching board and a monthly summary.</p>
                </div>
                <div class="view-switch">
                    <button class="switch-btn active" type="button" data-view-target="week-view"><i class="fa-regular fa-calendar-week"></i> Week View</button>
                    <button class="switch-btn" type="button" data-view-target="month-view"><i class="fa-regular fa-calendar-days"></i> Month View</button>
                </div>
            </div>

            <div id="week-view" class="view active">
                <div class="week-grid">
                    @foreach($weekView as $day => $slots)
                        <section class="day-card">
                            <div class="day-title">{{ $day }}</div>
                            @forelse($slots as $slot)
                                <article class="slot-card">
                                    <div class="slot-time">{{ $slot->time_range }}</div>
                                    <div class="muted" style="margin-top:8px;">{{ $slot->topic ?: 'Topic not set yet.' }}</div>
                                    <div class="muted" style="margin-top:6px;">{{ $slot->meeting_link ? 'Meeting link ready' : 'No meeting link' }}</div>
                                    <div class="slot-actions">
                                        <a href="{{ route('trainer.timetable.edit', $slot->id) }}" class="mini-btn secondary"><i class="fa-solid fa-pen"></i> Edit</a>
                                        <form method="POST" action="{{ route('trainer.timetable.delete', $slot->id) }}" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="mini-btn danger" onclick="return confirm('Delete this timetable slot?')"><i class="fa-solid fa-trash"></i> Delete</button>
                                        </form>
                                    </div>
                                </article>
                            @empty
                                <p class="muted" style="margin:0;">No classes scheduled.</p>
                            @endforelse
                        </section>
                    @endforeach
                </div>
            </div>

            <div id="month-view" class="view">
                <div class="month-grid">
                    @foreach($monthSummary as $day => $summary)
                        <section class="day-card month-cell">
                            <h3>{{ $day }}</h3>
                            <div class="month-count">{{ $summary['count'] }}</div>
                            <div class="muted">scheduled session{{ $summary['count'] === 1 ? '' : 's' }}</div>
                            @if($summary['topics']->isNotEmpty())
                                <div class="muted" style="margin-top:12px;">
                                    Focus: {{ $summary['topics']->implode(', ') }}
                                </div>
                            @else
                                <div class="muted" style="margin-top:12px;">No topics assigned yet.</div>
                            @endif
                        </section>
                    @endforeach
                </div>
            </div>
        </div>

        @if($timetables->isEmpty())
            <div class="empty-state" style="margin-top:20px;">
                <h2 style="margin:0 0 8px;color:#f8fafc;">No timetable entries yet</h2>
                <p class="muted" style="margin:0;">Add your first slot to start building the class week and month schedule.</p>
            </div>
        @endif
    </div>

    <script>
        const switchButtons = document.querySelectorAll('[data-view-target]');
        const views = document.querySelectorAll('.view');

        switchButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const target = button.dataset.viewTarget;

                switchButtons.forEach((item) => item.classList.toggle('active', item === button));
                views.forEach((view) => view.classList.toggle('active', view.id === target));
            });
        });
    </script>
</body>
</html>
