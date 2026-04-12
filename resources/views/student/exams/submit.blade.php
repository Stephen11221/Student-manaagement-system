<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Submit Exam | {{ config('app.name', 'School Portal') }}</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body{font-family:"Instrument Sans",sans-serif;background:linear-gradient(135deg,#020617 0%,#0f172a 54%,#111827 100%);color:#e2e8f0;min-height:100vh;margin:0}
        .container{max-width:760px;margin:0 auto;padding:40px 20px}.card{background:rgba(15,23,42,.78);border:1px solid rgba(148,163,184,.18);border-radius:16px;padding:28px;backdrop-filter:blur(18px)}
        header{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid rgba(148,163,184,.1)}
        .back-btn{background:rgba(34,211,238,.1);color:#22d3ee;border:1px solid rgba(34,211,238,.3);padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:600}
        .info{background:rgba(34,211,238,.08);border-left:4px solid #22d3ee;border-radius:8px;padding:16px;margin-bottom:24px}.meta{color:#94a3b8;font-size:.85rem;display:flex;gap:18px;flex-wrap:wrap}
        textarea,input[type="file"]{width:100%;padding:12px;border-radius:8px;border:1px solid rgba(148,163,184,.2);background:rgba(2,6,23,.56);color:#f8fafc;font-family:inherit}.textarea{min-height:220px;resize:vertical}
        .summary{background:rgba(15,118,110,.12);border:1px solid rgba(45,212,191,.22);border-radius:12px;padding:18px;margin-bottom:24px}.btn{display:inline-flex;align-items:center;gap:8px;padding:12px 18px;border-radius:8px;text-decoration:none;font-weight:700;border:none;cursor:pointer}.primary{background:linear-gradient(135deg,#22d3ee,#06b6d4);color:#082f49}.secondary{background:rgba(148,163,184,.1);color:#94a3b8;border:1px solid rgba(148,163,184,.2)}
        .error-message{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.32);border-radius:12px;padding:14px 16px;margin-bottom:18px;color:#fecaca}.hint{color:#94a3b8;font-size:.85rem;margin-top:8px}
    </style>
</head>
<body>
    @php
        $existingAnswers = old('answers', $submission?->answers_json ?? []);
        $submissionLocked = (bool) $submission;
    @endphp
    <div class="container">
        <header>
            <h1 style="margin:0;color:#f8fafc;"><i class="fa-solid fa-file-signature"></i> {{ $exam->title }}</h1>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('dashboard') }}" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
                <a href="{{ route('student.exams.index') }}" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back</a>
            </div>
        </header>
        <div class="card">
            <div class="info">
                <div style="font-size:1.15rem;font-weight:700;color:#f8fafc;">{{ $exam->title }}</div>
                <div style="margin:8px 0 10px;">{{ $exam->description }}</div>
                <div class="meta">
                    <span><i class="fa-regular fa-calendar"></i> {{ $exam->exam_date ? $exam->exam_date->format('M d, Y') : 'No exam date' }}</span>
                    <span><i class="fa-solid fa-list-check"></i> {{ $exam->isOnline() ? 'Online exam' : ucfirst($exam->submission_type === 'upload' ? 'file' : $exam->submission_type) }}</span>
                    <span><i class="fa-solid fa-circle-info"></i> {{ ucfirst($exam->exam_mode ?? 'online') }} exam</span>
                    <span><i class="fa-solid fa-chalkboard-user"></i> {{ $exam->class?->trainer?->name ?? 'Trainer' }}</span>
                </div>
            </div>

            @if($submission)
                <div class="summary">
                    <strong><i class="fa-solid fa-circle-info"></i> Current submission</strong>
                    <div style="margin-top:10px;color:#cbd5e1;">Status: {{ ucfirst($submission->status ?? 'draft') }}{{ $submission->marks !== null ? ' • Marks: '.$submission->marks.'%' : '' }}</div>
                    <div style="margin-top:6px;color:#cbd5e1;">Submitted: {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y H:i') : 'Not yet submitted' }}</div>
                    @if($submission->feedback)
                        <div style="margin-top:12px;white-space:pre-wrap;">{{ $submission->feedback }}</div>
                    @endif
                    @if($submission->file_path)
                        <div style="margin-top:12px;"><a href="{{ asset('storage/'.$submission->file_path) }}" class="back-btn" target="_blank"><i class="fa-solid fa-download"></i> View current file</a></div>
                    @endif
                </div>
            @endif

            @if (session('error'))
                <div class="error-message">
                    <strong><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</strong>
                </div>
            @endif
            @unless($submissionLocked)
                <form method="POST" action="{{ route('student.exams.store', $exam->id) }}" enctype="multipart/form-data">
                    @csrf
                    @if($errors->any())
                        <div class="error-message">
                            <strong><i class="fa-solid fa-triangle-exclamation"></i> Please fix the errors:</strong>
                            <ul style="margin-top:8px; margin-left:20px;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($exam->isOnline())
                        <div class="summary">
                            <strong><i class="fa-solid fa-circle-info"></i> Answer all questions below</strong>
                            <div style="margin-top:8px;color:#cbd5e1;">This online exam contains {{ $exam->questions->count() }} question{{ $exam->questions->count() === 1 ? '' : 's' }}.</div>
                        </div>

                        @forelse($exam->questions as $question)
                            <div style="margin-bottom:18px;">
                                <label style="display:block;margin-bottom:8px;color:#dbeafe;font-weight:700;">Question {{ $loop->iteration }}</label>
                                <div class="info" style="margin-bottom:10px;">
                                    <div style="margin:0;color:#f8fafc;">{{ $question->question_text }}</div>
                                </div>
                                <textarea class="textarea" name="answers[{{ $question->id }}]" required>{{ $existingAnswers[$question->id] ?? '' }}</textarea>
                            </div>
                        @empty
                            <div class="error-message">
                                <strong><i class="fa-solid fa-triangle-exclamation"></i> No questions have been added yet.</strong>
                                <div style="margin-top:8px;">Please check back once your trainer publishes the online questions.</div>
                            </div>
                        @endforelse
                    @elseif($exam->submission_type === 'written')
                        <label style="display:block;margin-bottom:8px;color:#dbeafe;font-weight:700;">Type your exam answer</label>
                        <textarea class="textarea" name="content" required>{{ old('content', '') }}</textarea>
                    @else
                        <label style="display:block;margin-bottom:8px;color:#dbeafe;font-weight:700;">Upload your exam file</label>
                        <input type="file" name="file" required>
                        <div class="hint">Max {{ number_format(submissionUploadMaxKilobytes() / 1024, 0) }}MB.</div>
                        @error('file')
                            <div style="color:#ef4444;font-size:.85rem;margin-top:8px;">{{ $message }}</div>
                        @enderror
                    @endif

                    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:24px;">
                        <button type="submit" class="btn primary"><i class="fa-solid fa-circle-check"></i> Submit Exam</button>
                        <a href="{{ route('student.exams.index') }}" class="btn secondary"><i class="fa-solid fa-arrow-left"></i> Cancel</a>
                    </div>
                </form>
            @else
                <div class="summary" style="margin-top:0;">
                    <strong><i class="fa-solid fa-lock"></i> Submission locked</strong>
                    <div style="margin-top:8px;color:#cbd5e1;">You have already submitted this exam. Updates are not allowed.</div>
                </div>
            @endunless
        </div>
    </div>
</body>
</html>
