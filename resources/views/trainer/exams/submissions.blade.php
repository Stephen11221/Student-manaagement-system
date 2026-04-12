<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exam Submissions</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body{font-family:"Instrument Sans",sans-serif;background:linear-gradient(135deg,#020617,#0f172a 54%,#111827 100%);color:#e2e8f0;margin:0;min-height:100vh}
        .container{max-width:1200px;margin:0 auto;padding:40px 20px}.card{background:rgba(15,23,42,.78);border:1px solid rgba(148,163,184,.18);border-radius:18px;padding:24px}
        .submission{padding:18px 0;border-bottom:1px solid rgba(148,163,184,.1)}.submission:last-child{border-bottom:none}.btn{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:8px;text-decoration:none;font-weight:700;border:none;cursor:pointer}.secondary{background:rgba(34,211,238,.12);color:#22d3ee;border:1px solid rgba(34,211,238,.3)}.primary{background:#22d3ee;color:#082f49}
        input,textarea{width:100%;padding:10px;border-radius:8px;border:1px solid rgba(148,163,184,.2);background:rgba(2,6,23,.56);color:#f8fafc;font-family:inherit}textarea{min-height:100px;resize:vertical}.grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px;align-items:start}.muted{color:#94a3b8}
        .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px;margin:16px 0 18px}.stat{background:rgba(2,6,23,.38);border:1px solid rgba(148,163,184,.16);border-radius:14px;padding:14px 16px}.stat-value{font-size:1.6rem;font-weight:800;color:#f8fafc;margin-top:6px}.pill{display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border-radius:999px;font-size:.8rem;font-weight:800}.pill-pass{background:rgba(16,185,129,.16);color:#86efac}.pill-fail{background:rgba(239,68,68,.16);color:#fca5a5}.pill-pending{background:rgba(148,163,184,.16);color:#cbd5e1}
        table{width:100%;border-collapse:collapse;margin-top:14px}.table-wrap{overflow:auto;border-radius:14px;border:1px solid rgba(148,163,184,.12)}th,td{padding:12px 14px;text-align:left;border-bottom:1px solid rgba(148,163,184,.1);vertical-align:top}th{background:rgba(34,211,238,.1);color:#dbeafe;font-size:.82rem;text-transform:uppercase;letter-spacing:.06em}.result-pass{color:#86efac}.result-fail{color:#fca5a5}.result-pending{color:#cbd5e1}
    </style>
</head>
<body>
    <div class="container">
        <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:16px;">
            <div>
                <h1 style="margin:0;color:#f8fafc;"><i class="fa-solid fa-graduation-cap"></i> {{ $exam->title }}</h1>
                <p class="muted">Grade exam submissions.</p>
                <p class="muted" style="margin-top:8px;"><i class="fa-solid fa-circle-info"></i> {{ ucfirst($exam->exam_mode ?? 'online') }} exam</p>
            </div>
            <a href="{{ route('trainer.exams.index', $exam->class_id) }}" class="btn secondary"><i class="fa-solid fa-arrow-left"></i> Back to Exams</a>
        </div>
        @if(session('status'))
            <div class="card" style="margin-bottom:16px;color:#86efac;"><i class="fa-solid fa-circle-check"></i> {{ session('status') }}</div>
        @endif
        @php
            $gradedCount = $submissions->where('status', 'graded')->count();
            $passedCount = $submissions->where('status', 'graded')->filter(fn ($sub) => (int) $sub->marks >= ($passMark ?? 50))->count();
            $failedCount = $submissions->where('status', 'graded')->filter(fn ($sub) => (int) $sub->marks < ($passMark ?? 50))->count();
            $pendingCount = $submissions->where('status', '!=', 'graded')->count();
        @endphp
        <div class="stats">
            <div class="stat">
                <div class="muted">Pass mark</div>
                <div class="stat-value">{{ $passMark ?? 50 }}%</div>
            </div>
            <div class="stat">
                <div class="muted">Graded</div>
                <div class="stat-value">{{ $gradedCount }}</div>
            </div>
            <div class="stat">
                <div class="muted">Passed</div>
                <div class="stat-value" style="color:#86efac;">{{ $passedCount }}</div>
            </div>
            <div class="stat">
                <div class="muted">Failed</div>
                <div class="stat-value" style="color:#fca5a5;">{{ $failedCount }}</div>
            </div>
            <div class="stat">
                <div class="muted">Pending</div>
                <div class="stat-value" style="color:#cbd5e1;">{{ $pendingCount }}</div>
            </div>
        </div>
        <div class="card">
            @forelse($submissions as $sub)
                <div class="submission">
                    <div class="grid">
                        <div>
                            <div style="font-weight:700;color:#f8fafc;">{{ $sub->student->name }}</div>
                            <div class="muted">{{ $sub->student->email }}</div>
                            <div class="muted" style="margin-top:8px;">Submitted: {{ $sub->submitted_at ? $sub->submitted_at->format('M d, Y H:i') : 'Pending' }}</div>
                            @if($exam->isOnline() && $sub->answers_json)
                                <div style="margin-top:12px;display:grid;gap:10px;">
                                    @foreach($exam->questions as $question)
                                        <div style="padding:12px;border-radius:10px;background:rgba(2,6,23,.45);">
                                            <div style="font-weight:700;color:#f8fafc;margin-bottom:6px;">Q{{ $loop->iteration }}. {{ $question->question_text }}</div>
                                            <div style="color:#cbd5e1;white-space:pre-wrap;">{{ $sub->answers_json[$question->id] ?? 'No answer submitted' }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($sub->content)
                                <div style="margin-top:12px;padding:12px;border-radius:10px;background:rgba(2,6,23,.45);white-space:pre-wrap;">{{ $sub->content }}</div>
                            @endif
                            @if($sub->file_path)
                                <div style="margin-top:12px;">
                                    <a href="{{ asset('storage/'.$sub->file_path) }}" target="_blank" class="btn secondary"><i class="fa-solid fa-download"></i> View Uploaded Exam</a>
                                </div>
                            @endif
                        </div>
                        <div>
                            <form method="POST" action="{{ route('trainer.exams.grade', $sub->id) }}">
                                @csrf
                                <label style="display:block;margin-bottom:8px;color:#dbeafe;font-weight:700;">Marks</label>
                                <input type="number" name="marks" value="{{ $sub->marks }}" max="100" min="0" required>
                                <label style="display:block;margin:14px 0 8px;color:#dbeafe;font-weight:700;">Feedback</label>
                                <textarea name="feedback">{{ $sub->feedback }}</textarea>
                                <button type="submit" class="btn primary" style="margin-top:14px;"><i class="fa-regular fa-floppy-disk"></i> Save Grade</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="muted">No exam submissions yet.</p>
            @endforelse
        </div>

        <div class="card" style="margin-top:16px;">
            <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:12px;">
                <div>
                    <h2 style="margin:0;color:#f8fafc;">Performance Table</h2>
                    <p class="muted" style="margin:6px 0 0;">Students who score {{ $passMark ?? 50 }}% and above pass.</p>
                </div>
                <div class="muted">After grading, the result updates automatically.</div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Marks</th>
                            <th>Result</th>
                            <th>Submitted</th>
                            <th>Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $sub)
                            @php
                                $result = $sub->status === 'graded'
                                    ? ((int) $sub->marks >= ($passMark ?? 50) ? 'Pass' : 'Fail')
                                    : 'Pending';
                            @endphp
                            <tr>
                                <td>
                                    <strong style="color:#f8fafc;">{{ $sub->student->name }}</strong>
                                    <div class="muted">{{ $sub->student->email }}</div>
                                </td>
                                <td>
                                    <strong style="color:#f8fafc;">{{ $sub->marks !== null ? $sub->marks.'%' : '—' }}</strong>
                                </td>
                                <td>
                                    @if($result === 'Pass')
                                        <span class="pill pill-pass"><i class="fa-solid fa-circle-check"></i> Pass</span>
                                    @elseif($result === 'Fail')
                                        <span class="pill pill-fail"><i class="fa-solid fa-circle-xmark"></i> Fail</span>
                                    @else
                                        <span class="pill pill-pending"><i class="fa-regular fa-clock"></i> Pending</span>
                                    @endif
                                </td>
                                <td class="muted">{{ $sub->submitted_at ? $sub->submitted_at->format('M d, Y H:i') : 'Pending' }}</td>
                                <td class="muted">{{ $sub->feedback ?: 'No feedback yet' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="muted" style="padding:16px;">No exam submissions yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
