<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Review | {{ $student->name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:"Instrument Sans",sans-serif; background:linear-gradient(135deg,#020617,#0f172a 54%,#111827 100%); color:#e2e8f0; min-height:100vh; }
        .container { max-width:1100px; margin:0 auto; padding:40px 20px; }
        .hero,.panel { background:rgba(15,23,42,.78); border:1px solid rgba(148,163,184,.18); border-radius:18px; padding:24px; backdrop-filter:blur(18px); }
        .hero { margin-bottom:24px; }
        h1,h2 { color:#f8fafc; }
        p,li { color:#94a3b8; }
        .meta { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:16px; margin-top:20px; }
        .meta-card { background:rgba(34,211,238,.06); border:1px solid rgba(34,211,238,.15); border-radius:14px; padding:16px; }
        .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:24px; }
        ul { margin-top:14px; padding-left:18px; }
        .btn { display:inline-flex; align-items:center; gap:8px; padding:10px 16px; border-radius:8px; text-decoration:none; font-weight:700; background:rgba(34,211,238,.1); color:#22d3ee; border:1px solid rgba(34,211,238,.3); margin-top:16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <h1><i class="fa-solid fa-user-check"></i> {{ $student->name }}</h1>
            <p style="margin-top: 8px;">Career coaching review profile</p>
            <div class="meta">
                <div class="meta-card"><strong style="color:#f8fafc;"><i class="fa-regular fa-envelope"></i> Email</strong><p style="margin-top:8px;">{{ $student->email }}</p></div>
                <div class="meta-card"><strong style="color:#f8fafc;"><i class="fa-solid fa-building"></i> Department</strong><p style="margin-top:8px;">{{ $student->department ?: 'Not assigned' }}</p></div>
                <div class="meta-card"><strong style="color:#f8fafc;"><i class="fa-solid fa-book"></i> Enrolled Classes</strong><p style="margin-top:8px;">{{ $student->enrolledClasses->count() }}</p></div>
                <div class="meta-card"><strong style="color:#f8fafc;"><i class="fa-solid fa-file-circle-check"></i> Submissions</strong><p style="margin-top:8px;">{{ $student->homeworkSubmissions->count() }}</p></div>
            </div>
            <a href="{{ route('dashboard') }}" class="btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
        </div>

        <div class="grid">
            <div class="panel">
                <h2><i class="fa-solid fa-school"></i> Current Classes</h2>
                <ul>
                    @forelse($student->enrolledClasses as $class)
                        <li>{{ $class->name }} with {{ $class->trainer->name }}</li>
                    @empty
                        <li>No active classes linked.</li>
                    @endforelse
                </ul>
            </div>
            <div class="panel">
                <h2><i class="fa-solid fa-list-check"></i> Recent Submission Activity</h2>
                <ul>
                    @forelse($student->homeworkSubmissions->sortByDesc('submitted_at')->take(5) as $submission)
                        <li>{{ optional($submission->homework)->title ?: 'Homework' }}: {{ ucfirst($submission->status) }}</li>
                    @empty
                        <li>No submissions recorded yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    @include('partials.chat-fab')
</body>
</html>
