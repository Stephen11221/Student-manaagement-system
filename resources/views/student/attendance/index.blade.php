<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendance</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family:"Instrument Sans",sans-serif; background:linear-gradient(135deg,#020617,#0f172a 54%,#111827 100%); color:#e2e8f0; margin:0; min-height:100vh; }
        .container { max-width:1200px; margin:0 auto; padding:40px 20px; }
        header { display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; border-bottom:1px solid rgba(148,163,184,.1); padding-bottom:20px; gap:12px; flex-wrap:wrap; }
        h1 { color:#f8fafc; margin:0; }
        .btn { background:rgba(34,211,238,.12); color:#22d3ee; border:1px solid rgba(34,211,238,.3); padding:10px 16px; border-radius:8px; text-decoration:none; font-weight:700; display:inline-flex; align-items:center; gap:8px; }
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:16px; margin-bottom:24px; }
        .stat { background:rgba(15,23,42,.78); border:1px solid rgba(148,163,184,.18); padding:20px; border-radius:16px; }
        .stat-num { font-size:2rem; color:#22d3ee; font-weight:700; margin-top:10px; }
        .table-wrap { background:rgba(15,23,42,.78); border:1px solid rgba(148,163,184,.18); border-radius:16px; overflow:hidden; }
        .table { width:100%; border-collapse:collapse; }
        th,td { padding:14px 16px; text-align:left; border-bottom:1px solid rgba(148,163,184,.1); vertical-align:top; }
        th { background:rgba(34,211,238,.1); color:#dbeafe; font-weight:700; }
        td { color:#e2e8f0; }
        .present { color:#34d399; font-weight:700; }
        .absent { color:#fb7185; font-weight:700; }
        .late { color:#f59e0b; font-weight:700; }
        .muted { color:#94a3b8; font-size:.9rem; }
        .empty-state { background:rgba(15,23,42,.78); border:1px solid rgba(148,163,184,.18); border-radius:16px; padding:32px; text-align:center; color:#94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fa-solid fa-clipboard-check"></i> Attendance Record</h1>
            <a href="{{ route('dashboard') }}" class="btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
        </header>

        <div class="stats-grid">
            <div class="stat">
                <div class="muted">Attendance Rate</div>
                <div class="stat-num">{{ round($attendancePercentage) }}%</div>
            </div>
            <div class="stat">
                <div class="muted">Present</div>
                <div class="stat-num" style="color:#34d399;">{{ $presentCount }}</div>
            </div>
            <div class="stat">
                <div class="muted">Late</div>
                <div class="stat-num" style="color:#f59e0b;">{{ $lateCount }}</div>
            </div>
            <div class="stat">
                <div class="muted">Absent</div>
                <div class="stat-num" style="color:#fb7185;">{{ $absentCount }}</div>
            </div>
        </div>

        @if ($attendance->count())
            <div class="table-wrap">
                <table class="table">
                    <tr>
                        <th>Class</th>
                        <th>Schedule</th>
                        <th>Marked</th>
                        <th>Status</th>
                    </tr>
                    @foreach ($attendance as $att)
                        <tr>
                            <td>
                                <div>{{ $att->classRoom?->name ?? 'Class unavailable' }}</div>
                                <div class="muted">{{ $att->classRoom?->room_number ? 'Room '.$att->classRoom->room_number : 'Room not set' }}</div>
                            </td>
                            <td>
                                <div>{{ $att->timetable?->day_of_week ?? 'Schedule unavailable' }}</div>
                                <div class="muted">
                                    @if ($att->timetable)
                                        {{ $att->timetable->time_range }}
                                    @else
                                        No timetable linked
                                    @endif
                                </div>
                            </td>
                            <td>{{ $att->marked_at ? $att->marked_at->format('M d, Y H:i') : 'Not recorded' }}</td>
                            <td class="{{ strtolower($att->status) }}">{{ ucfirst($att->status) }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fa-regular fa-calendar-xmark"></i> No attendance records yet.
            </div>
        @endif
    </div>
</body>
</html>
