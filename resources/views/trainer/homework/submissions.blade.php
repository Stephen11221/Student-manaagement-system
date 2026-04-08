<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Homework Submissions</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body{font-family:"Instrument Sans",sans-serif;background:linear-gradient(135deg,#020617,#0f172a 54%,#111827 100%);color:#e2e8f0;margin:0;min-height:100vh}
        .container{max-width:1200px;margin:0 auto;padding:40px 20px}.card{background:rgba(15,23,42,.78);border:1px solid rgba(148,163,184,.18);border-radius:18px;padding:24px}
        .submission{padding:18px 0;border-bottom:1px solid rgba(148,163,184,.1)}.submission:last-child{border-bottom:none}.btn{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:8px;text-decoration:none;font-weight:700;border:none;cursor:pointer}.secondary{background:rgba(34,211,238,.12);color:#22d3ee;border:1px solid rgba(34,211,238,.3)}.primary{background:#22d3ee;color:#082f49}
        input,textarea{width:100%;padding:10px;border-radius:8px;border:1px solid rgba(148,163,184,.2);background:rgba(2,6,23,.56);color:#f8fafc;font-family:inherit}textarea{min-height:100px;resize:vertical}.grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px;align-items:start}.muted{color:#94a3b8}
    </style>
</head>
<body>
    <div class="container">
        <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:16px;">
            <div>
                <h1 style="margin:0;color:#f8fafc;"><i class="fa-solid fa-list-check"></i> {{ $homework->title }}</h1>
                <p class="muted">Review and grade submissions.</p>
            </div>
            <a href="{{ route('trainer.homework.index', $homework->class_id) }}" class="btn secondary"><i class="fa-solid fa-arrow-left"></i> Back to Homework</a>
        </div>
        @if(session('status'))
            <div class="card" style="margin-bottom:16px;color:#86efac;"><i class="fa-solid fa-circle-check"></i> {{ session('status') }}</div>
        @endif
        <div class="card">
            @forelse($submissions as $sub)
                <div class="submission">
                    <div class="grid">
                        <div>
                            <div style="font-weight:700;color:#f8fafc;">{{ $sub->student->name }}</div>
                            <div class="muted">{{ $sub->student->email }}</div>
                            <div class="muted" style="margin-top:8px;">Submitted: {{ $sub->submitted_at ? $sub->submitted_at->format('M d, Y H:i') : 'Pending' }}</div>
                            @if($sub->content)
                                <div style="margin-top:12px;padding:12px;border-radius:10px;background:rgba(2,6,23,.45);white-space:pre-wrap;">{{ $sub->content }}</div>
                            @endif
                            @if($sub->file_path)
                                <div style="margin-top:12px;">
                                    <a href="{{ asset('storage/'.$sub->file_path) }}" target="_blank" class="btn secondary"><i class="fa-solid fa-download"></i> View Uploaded File</a>
                                </div>
                            @endif
                        </div>
                        <div>
                            <form method="POST" action="{{ route('trainer.homework.grade', $sub->id) }}">
                                @csrf
                                <label style="display:block;margin-bottom:8px;color:#dbeafe;font-weight:700;">Marks</label>
                                <input type="number" name="marks" value="{{ $sub->marks }}" max="100" min="0" required>
                                <label style="display:block;margin:14px 0 8px;color:#dbeafe;font-weight:700;">Feedback</label>
                                <textarea name="feedback" placeholder="Share strengths, corrections, or next steps.">{{ $sub->feedback }}</textarea>
                                <button type="submit" class="btn primary" style="margin-top:14px;"><i class="fa-regular fa-floppy-disk"></i> Save Grade</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="muted">No submissions yet.</p>
            @endforelse
        </div>
    </div>
</body>
</html>
