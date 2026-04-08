<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $mode === 'create' ? 'Create' : 'Edit' }} Timetable</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body{font-family:"Instrument Sans",sans-serif;background:linear-gradient(135deg,#020617,#0f172a 54%,#111827 100%);color:#e2e8f0;margin:0;min-height:100vh}
        .container{max-width:760px;margin:0 auto;padding:40px 20px}.card{background:rgba(15,23,42,.78);border:1px solid rgba(148,163,184,.18);border-radius:18px;padding:24px}
        .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}label{display:block;margin-bottom:8px;color:#dbeafe;font-weight:700}
        input,select{width:100%;padding:12px;border-radius:8px;border:1px solid rgba(148,163,184,.2);background:rgba(2,6,23,.56);color:#f8fafc}.full{grid-column:1/-1}
        .btn{display:inline-flex;align-items:center;gap:8px;padding:12px 18px;border-radius:8px;text-decoration:none;font-weight:700;border:none;cursor:pointer}.primary{background:linear-gradient(135deg,#22d3ee,#06b6d4);color:#082f49}.secondary{background:rgba(34,211,238,.12);color:#22d3ee;border:1px solid rgba(34,211,238,.3)}
        .hint{color:#94a3b8;font-size:.82rem;margin-top:6px}.preview{margin-top:18px;padding:14px 16px;border-radius:12px;background:rgba(34,211,238,.08);border:1px solid rgba(34,211,238,.18);color:#cbd5e1}.error-box{margin-bottom:18px;padding:12px 14px;border-radius:12px;background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.28);color:#fecaca}
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1 style="margin-top:0;color:#f8fafc;"><i class="fa-regular fa-calendar"></i> {{ $mode === 'create' ? 'Add' : 'Edit' }} Timetable Slot</h1>
            <p style="color:#94a3b8;">{{ $class->name }}</p>
            @if($errors->any())
                <div class="error-box"><i class="fa-solid fa-triangle-exclamation"></i> Please enter the timetable time in the correct order and format.</div>
            @endif
            <form method="POST" action="{{ $mode === 'create' ? route('trainer.timetable.store', $class->id) : route('trainer.timetable.update', $timetable->id) }}">
                @csrf
                <div class="grid">
                    <div>
                        <label>Day</label>
                        <select name="day_of_week" required>
                            @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                                <option value="{{ $day }}" @selected(old('day_of_week', $timetable->day_of_week) === $day)>{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Topic</label>
                        <input type="text" name="topic" value="{{ old('topic', $timetable->topic) }}" placeholder="Lesson topic">
                    </div>
                    <div>
                        <label>Start Time</label>
                        <input type="time" name="start_time" value="{{ old('start_time', $timetable->start_time) }}" required>
                        <div class="hint">Use 24-hour input. It will display as 08:00 AM.</div>
                    </div>
                    <div>
                        <label>End Time</label>
                        <input type="time" name="end_time" value="{{ old('end_time', $timetable->end_time) }}" required>
                        <div class="hint">End time must be later than start time.</div>
                    </div>
                    <div class="full">
                        <label>Meeting Link</label>
                        <input type="url" name="meeting_link" value="{{ old('meeting_link', $timetable->meeting_link) }}" placeholder="https://...">
                    </div>
                </div>
                <div class="preview">
                    <i class="fa-regular fa-clock"></i>
                    Saved display format:
                    <strong>
                        {{ old('day_of_week', $timetable->day_of_week ?: 'Monday') }}
                        •
                        {{ $timetable->formatted_start_time ?? '08:00 AM' }} - {{ $timetable->formatted_end_time ?? '09:00 AM' }}
                    </strong>
                </div>
                <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:24px;">
                    <button type="submit" class="btn primary"><i class="fa-regular fa-floppy-disk"></i> {{ $mode === 'create' ? 'Create Slot' : 'Save Changes' }}</button>
                    <a href="{{ route('trainer.timetable.index', $class->id) }}" class="btn secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
