<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendance | {{ $class->name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:"Instrument Sans",sans-serif; background:linear-gradient(135deg,#020617,#0f172a 54%,#111827 100%); color:#e2e8f0; min-height:100vh; }
        .container { max-width:980px; margin:0 auto; padding:40px 20px; }
        .panel { background:rgba(15,23,42,.78); border:1px solid rgba(148,163,184,.18); border-radius:18px; padding:24px; backdrop-filter:blur(18px); }
        h1 { color:#f8fafc; margin-bottom:8px; }
        p, label { color:#94a3b8; }
        .alert { margin:16px 0; padding:12px 16px; border-radius:10px; }
        .alert.success { background:rgba(16,185,129,.12); color:#10b981; border:1px solid rgba(16,185,129,.24); }
        .alert.error { background:rgba(239,68,68,.12); color:#ef4444; border:1px solid rgba(239,68,68,.24); }
        .student-row { display:grid; grid-template-columns:1.3fr 1fr; gap:16px; align-items:center; padding:16px 0; border-bottom:1px solid rgba(148,163,184,.1); }
        .student-row:last-child { border-bottom:none; }
        select { width:100%; padding:12px; border-radius:8px; background:rgba(2,6,23,.56); color:#f8fafc; border:1px solid rgba(148,163,184,.2); }
        .actions { display:flex; justify-content:space-between; gap:12px; margin-top:24px; flex-wrap:wrap; }
        .quick-actions { display:flex; gap:10px; flex-wrap:wrap; margin:18px 0 8px; }
        .btn { display:inline-flex; align-items:center; gap:8px; padding:12px 16px; border-radius:8px; font-weight:700; text-decoration:none; border:none; cursor:pointer; }
        .btn-primary { background:linear-gradient(135deg,#22d3ee,#06b6d4); color:#082f49; }
        .btn-secondary { background:rgba(148,163,184,.1); color:#94a3b8; border:1px solid rgba(148,163,184,.2); }
        .btn-ghost { background:rgba(34,211,238,.08); color:#67e8f9; border:1px solid rgba(34,211,238,.18); }
    </style>
</head>
<body>
    <div class="container">
        <div class="panel">
            <h1><i class="fa-solid fa-clipboard-user"></i> Attendance for {{ $class->name }}</h1>
            <p>Mark each student as present, absent, or late. Attendance will also generate student notifications.</p>
            @if($class->timetables->count())
                <p style="margin-top:8px;">Current timetable reference: <strong style="color:#dbeafe;">{{ $class->timetables->first()->day_of_week }} • {{ $class->timetables->first()->time_range }}</strong></p>
            @endif

            @if (session('status'))
                <div class="alert success"><i class="fa-solid fa-circle-check"></i> {{ session('status') }}</div>
            @endif

            @if (session('error'))
                <div class="alert error"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('trainer.attendance.store', $class->id) }}">
                @csrf
                @if($class->students->count())
                    <div class="quick-actions">
                        <button type="button" class="btn btn-ghost" onclick="setAllAttendance('present')"><i class="fa-solid fa-user-check"></i> Mark All Present</button>
                        <button type="button" class="btn btn-ghost" onclick="setAllAttendance('late')"><i class="fa-solid fa-user-clock"></i> Mark All Late</button>
                        <button type="button" class="btn btn-ghost" onclick="setAllAttendance('absent')"><i class="fa-solid fa-user-xmark"></i> Mark All Absent</button>
                    </div>
                @endif
                @forelse($class->students as $student)
                    @php
                        $record = $attendance->get($student->id);
                        $value = old('attendance.' . $student->id, optional($record)->status ?? 'present');
                    @endphp
                    <div class="student-row">
                        <div>
                            <div style="color:#f8fafc; font-weight:700;">{{ $student->name }}</div>
                            <div style="margin-top:4px;">{{ $student->email }}</div>
                        </div>
                        <div>
                            <label for="student-{{ $student->id }}">Status</label>
                            <select id="student-{{ $student->id }}" name="attendance[{{ $student->id }}]">
                                <option value="present" @selected($value === 'present')>Present</option>
                                <option value="late" @selected($value === 'late')>Late</option>
                                <option value="absent" @selected($value === 'absent')>Absent</option>
                            </select>
                        </div>
                    </div>
                @empty
                    <p style="margin-top:20px;">No students are enrolled in this class yet.</p>
                @endforelse

                <div class="actions">
                    <a href="{{ route('trainer.classes.show', $class->id) }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back to Class</a>
                    <button type="submit" class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i> Save Attendance</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function setAllAttendance(status) {
            document.querySelectorAll('select[name^="attendance["]').forEach((select) => {
                select.value = status;
            });
        }
    </script>
</body>
</html>
