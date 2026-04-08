<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $mode === 'create' ? 'Create' : 'Edit' }} Exam</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body{font-family:"Instrument Sans",sans-serif;background:linear-gradient(135deg,#020617,#0f172a 54%,#111827 100%);color:#e2e8f0;margin:0;min-height:100vh}
        .container{max-width:820px;margin:0 auto;padding:40px 20px}.card{background:rgba(15,23,42,.78);border:1px solid rgba(148,163,184,.18);border-radius:18px;padding:24px}
        .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}label{display:block;margin-bottom:8px;color:#dbeafe;font-weight:700}
        input,select,textarea{width:100%;padding:12px;border-radius:8px;border:1px solid rgba(148,163,184,.2);background:rgba(2,6,23,.56);color:#f8fafc;font-family:inherit}textarea{min-height:140px;resize:vertical}.full{grid-column:1/-1}
        .btn{display:inline-flex;align-items:center;gap:8px;padding:12px 18px;border-radius:8px;text-decoration:none;font-weight:700;border:none;cursor:pointer}.primary{background:linear-gradient(135deg,#22d3ee,#06b6d4);color:#082f49}.secondary{background:rgba(34,211,238,.12);color:#22d3ee;border:1px solid rgba(34,211,238,.3)}
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1 style="margin-top:0;color:#f8fafc;"><i class="fa-solid fa-file-signature"></i> {{ $mode === 'create' ? 'Create' : 'Edit' }} Exam</h1>
            <p style="color:#94a3b8;">{{ $class->name }}</p>
            <form method="POST" action="{{ $mode === 'create' ? route('trainer.exams.store', $class->id) : route('trainer.exams.update', $exam->id) }}">
                @csrf
                <div class="grid">
                    <div class="full">
                        <label>Title</label>
                        <input type="text" name="title" value="{{ old('title', $exam->title) }}" required>
                    </div>
                    <div>
                        <label>Exam Date</label>
                        <input type="date" name="exam_date" value="{{ old('exam_date', optional($exam->exam_date)->toDateString()) }}">
                    </div>
                    <div>
                        <label>Status</label>
                        <select name="status" required>
                            <option value="open" @selected(old('status', $exam->status ?? 'open') === 'open')>Open</option>
                            <option value="closed" @selected(old('status', $exam->status) === 'closed')>Closed</option>
                        </select>
                    </div>
                    <div class="full">
                        <label>Submission Type</label>
                        <select name="submission_type" required>
                            <option value="written" @selected(old('submission_type', $exam->submission_type) === 'written')>Student types exam answers</option>
                            <option value="file" @selected(in_array(old('submission_type', $exam->submission_type), ['file', 'upload'], true))>Student uploads exam file</option>
                        </select>
                    </div>
                    <div class="full">
                        <label>Description</label>
                        <textarea name="description" required>{{ old('description', $exam->description) }}</textarea>
                    </div>
                </div>
                <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:24px;">
                    <button type="submit" class="btn primary"><i class="fa-regular fa-floppy-disk"></i> {{ $mode === 'create' ? 'Create Exam' : 'Save Changes' }}</button>
                    <a href="{{ route('trainer.exams.index', $class->id) }}" class="btn secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
