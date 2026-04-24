<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Submit Homework | {{ config('app.name', 'School Portal') }}</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --surface: rgba(15, 23, 42, 0.94);
            --surface-strong: rgba(30, 41, 59, 0.98);
            --border: rgba(51, 65, 85, 0.95);
            --text: #f8fafc;
            --muted: #cbd5e1;
            --primary: #38bdf8;
            --primary-strong: #0ea5e9;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        * { box-sizing:border-box; margin:0; padding:0; }
        body {
            font-family:"Instrument Sans",sans-serif;
            background:
                radial-gradient(circle at top left, rgba(56, 189, 248, 0.14), transparent 24%),
                radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.1), transparent 22%),
                linear-gradient(135deg, #020617 0%, #0f172a 56%, #111827 100%);
            color:var(--text);
            min-height:100vh;
        }

        .page-shell { width:min(1440px, calc(100% - 32px)); margin:0 auto; padding:40px 0 48px; }
        .page-header {
            display:grid; gap:20px; grid-template-columns:minmax(0,1fr) auto;
            align-items:end; padding-bottom:24px; border-bottom:1px solid var(--border); margin-bottom:24px;
        }
        .eyebrow {
            display:inline-flex; align-items:center; gap:8px; border-radius:999px;
            border:1px solid rgba(56,189,248,.28); background:rgba(56,189,248,.12); color:#cffafe;
            padding:8px 12px; font-size:.78rem; font-weight:800; letter-spacing:.18em; text-transform:uppercase;
        }
        h1,p { margin:0; }
        .page-title { margin-top:12px; font-size:clamp(2rem,3.4vw,3rem); line-height:1.05; font-weight:800; }
        .page-copy { margin-top:12px; max-width:72ch; color:var(--muted); line-height:1.7; }
        .btn {
            display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:16px;
            padding:12px 16px; border:1px solid transparent; text-decoration:none; font-weight:800;
            transition:transform 160ms ease, background 160ms ease, border-color 160ms ease;
        }
        .btn:hover { transform:translateY(-1px); }
        .btn-primary { background:linear-gradient(135deg,var(--primary),var(--primary-strong)); color:#082f49; }
        .btn-secondary { background:rgba(15,23,42,.92); color:var(--text); border-color:var(--border); }
        .surface {
            background:var(--surface); border:1px solid var(--border); border-radius:28px;
            box-shadow:0 24px 80px rgba(2,6,23,.34); backdrop-filter:blur(18px); padding:24px;
        }
        .layout { display:grid; gap:24px; grid-template-columns:minmax(0,1.1fr) minmax(320px,0.9fr); }
        .section-title { display:flex; align-items:center; gap:10px; font-size:1.05rem; font-weight:800; color:var(--text); }
        .section-subtitle { margin-top:6px; color:var(--muted); }
        .assignment-info {
            border-radius:24px; border:1px solid rgba(6,182,212,.28); background:rgba(6,182,212,.1); padding:18px; margin-bottom:18px;
        }
        .assignment-head { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; }
        .assignment-title { color:var(--text); font-size:1.2rem; font-weight:800; margin-bottom:8px; }
        .assignment-desc { color:#cbd5e1; font-size:.95rem; line-height:1.6; }
        .assignment-meta { display:flex; gap:12px; flex-wrap:wrap; margin-top:12px; color:#e2e8f0; }
        .chip {
            display:inline-flex; align-items:center; gap:8px; border-radius:999px; padding:8px 12px;
            font-size:.82rem; font-weight:800; border:1px solid transparent;
        }
        .chip-class { background:rgba(59,130,246,.14); color:#bfdbfe; border-color:rgba(59,130,246,.28); }
        .chip-due { background:rgba(245,158,11,.14); color:#fde68a; border-color:rgba(245,158,11,.28); }
        .chip-status { background:rgba(34,197,94,.14); color:#bbf7d0; border-color:rgba(34,197,94,.28); }
        .form-section { margin-bottom:24px; }
        label { display:block; margin-bottom:8px; color:#e2e8f0; font-weight:700; font-size:.95rem; }
        textarea {
            width:100%; padding:14px; border:1px solid var(--border); border-radius:14px;
            background:rgba(2,6,23,.62); color:var(--text); font:inherit; font-size:.95rem;
            resize:vertical; min-height:200px; transition:.2s;
        }
        textarea:focus { outline:none; border-color:rgba(56,189,248,.65); box-shadow:0 0 0 3px rgba(56,189,248,.12); }
        .file-upload-box {
            position:relative; border:2px dashed rgba(56,189,248,.34); border-radius:20px; padding:36px 20px;
            text-align:center; cursor:pointer; transition:.2s; background:rgba(56,189,248,.05);
        }
        .file-upload-box:hover { border-color:rgba(56,189,248,.7); background:rgba(56,189,248,.09); }
        .file-upload-box.dragover { border-color:rgba(56,189,248,.8); background:rgba(56,189,248,.14); }
        #file-input { display:none; }
        .upload-icon { font-size:2.5rem; margin-bottom:12px; color:var(--primary); }
        .upload-text { color:var(--text); font-weight:800; margin-bottom:4px; }
        .upload-hint { color:var(--muted); font-size:.85rem; }
        .file-list { margin-top:16px; }
        .file-item {
            background:rgba(16,185,129,.1); border:1px solid rgba(34,197,94,.28); border-radius:14px;
            padding:12px; display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:8px;
        }
        .file-name { color:var(--text); font-weight:700; flex:1; }
        .file-size { color:var(--muted); font-size:.85rem; margin-right:12px; }
        .remove-file {
            background:rgba(239,68,68,.12); color:#fecaca; border:1px solid rgba(239,68,68,.28);
            padding:6px 10px; border-radius:10px; cursor:pointer; font-size:.85rem; font-weight:800;
        }
        .button-group { display:flex; gap:12px; margin-top:28px; padding-top:24px; border-top:1px solid rgba(51,65,85,.95); }
        .error-message {
            background:rgba(239,68,68,.14); border:1px solid rgba(239,68,68,.28); border-radius:14px;
            padding:14px 16px; color:#fecaca; margin-bottom:16px; font-size:.9rem;
        }
        .success-message {
            background:rgba(34,197,94,.14); border:1px solid rgba(34,197,94,.28); border-radius:14px;
            padding:14px 16px; color:#bbf7d0; margin-bottom:16px; font-size:.9rem;
        }
        .summary {
            display:grid; gap:14px;
        }
        .summary-panel {
            background:var(--surface-strong); border:1px solid var(--border); border-radius:22px; padding:18px;
        }
        .summary-label {
            color:var(--muted); font-size:.78rem; font-weight:800; text-transform:uppercase; letter-spacing:.12em;
        }
        .summary-value { margin-top:8px; color:var(--text); font-size:1.2rem; font-weight:800; }
        .summary-copy { margin-top:8px; color:var(--muted); line-height:1.65; }
        @media (max-width: 1000px) {
            .page-header, .layout { grid-template-columns:1fr; }
            .assignment-head { flex-direction:column; }
        }
        @media (max-width: 640px) {
            .page-shell { width:calc(100% - 24px); padding-top:24px; }
            .button-group { flex-direction:column; }
            .btn { width:100%; }
            .surface { padding:18px; }
        }
    </style>
</head>
<body>
    <div class="page-shell">
        <header class="page-header">
            <div>
                <p class="eyebrow"><i class="fa-solid fa-file-pen"></i> Homework submission</p>
                <h1 class="page-title">Submit Homework</h1>
                <p class="page-copy">Upload your work with clear instructions and visible file requirements. The page keeps the assignment context, upload tools, and save buttons in one high-contrast layout.</p>
            </div>
            <div>
                <a href="{{ route('student.homework.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back to assignments</a>
            </div>
        </header>

        @if ($errors->any())
            <div class="error-message">
                <strong><i class="fa-solid fa-triangle-exclamation"></i> Please fix the errors below:</strong>
                <ul style="margin-top:8px; margin-left:20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="success-message">
                <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
            </div>
        @endif

        <div class="layout">
            <section class="surface">
                <div class="assignment-info">
                    <div class="assignment-head">
                        <div>
                            <div class="section-title"><i class="fa-solid fa-book"></i> Assignment details</div>
                            <div class="assignment-title">{{ $homework->title }}</div>
                            <div class="assignment-desc">{{ $homework->description ?: 'No assignment instructions were added yet.' }}</div>
                        </div>
                        <div class="chips">
                            <span class="chip chip-class"><i class="fa-solid fa-school"></i> {{ $homework->class?->name ?? 'Class unavailable' }}</span>
                            <span class="chip chip-due"><i class="fa-regular fa-calendar"></i> {{ $homework->due_date ? $homework->due_date->format('M d, Y') : 'No deadline' }}</span>
                        </div>
                    </div>
                    <div class="assignment-meta">
                        <span class="chip chip-status"><i class="fa-solid fa-circle-check"></i> {{ ucfirst($homework->submission_type === 'upload' ? 'File upload' : $homework->submission_type) }}</span>
                        @if ($homework->class?->trainer)
                            <span class="chip" style="background:rgba(245,158,11,.14); color:#fde68a; border-color:rgba(245,158,11,.28);"><i class="fa-solid fa-chalkboard-user"></i> {{ $homework->class->trainer->name }}</span>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route('student.homework.store', $homework->id) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-section">
                        <label for="submission_note"><i class="fa-solid fa-message"></i> Submission note</label>
                        <textarea id="submission_note" name="submission_note" placeholder="Add a short note for your trainer">{{ old('submission_note') }}</textarea>
                    </div>

                    <div class="form-section">
                        <label><i class="fa-solid fa-file-arrow-up"></i> Upload files</label>
                        <label for="file-input" class="file-upload-box" id="drop-zone">
                            <div class="upload-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                            <div class="upload-text">Click or drop files here</div>
                            <div class="upload-hint">Accepted formats depend on your school rules. Keep filenames clear and readable.</div>
                        </label>
                        <input type="file" id="file-input" name="file" multiple>
                        <div class="file-list" id="file-list"></div>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Submit Work</button>
                        <a href="{{ route('student.homework.index') }}" class="btn btn-secondary"><i class="fa-solid fa-xmark"></i> Cancel</a>
                    </div>
                </form>
            </section>

            <aside class="summary">
                <div class="summary-panel">
                    <div class="summary-label">Status</div>
                    <div class="summary-value">Ready to submit</div>
                    <div class="summary-copy">Use this form to send your work and keep a clear record of your submission note and attached files.</div>
                </div>
                <div class="summary-panel">
                    <div class="summary-label">Deadline</div>
                    <div class="summary-value">{{ $homework->due_date ? $homework->due_date->format('M d, Y') : 'No deadline' }}</div>
                    <div class="summary-copy">Submit before the deadline to avoid late status and possible penalties.</div>
                </div>
                <div class="summary-panel">
                    <div class="summary-label">Checklist</div>
                    <div class="summary-copy">
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;"><i class="fa-solid fa-circle-check" style="color:#86efac;"></i> File opens correctly</div>
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;"><i class="fa-solid fa-circle-check" style="color:#86efac;"></i> File name is clear</div>
                        <div style="display:flex; align-items:center; gap:8px;"><i class="fa-solid fa-circle-check" style="color:#86efac;"></i> Note is short and complete</div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('file-input');
        const fileList = document.getElementById('file-list');
        const dropZone = document.getElementById('drop-zone');

        function renderFiles() {
            fileList.innerHTML = '';
            Array.from(fileInput.files).forEach((file, index) => {
                const row = document.createElement('div');
                row.className = 'file-item';
                row.innerHTML = `
                    <span class="file-name"><i class="fa-solid fa-file"></i> ${file.name}</span>
                    <span class="file-size">${Math.ceil(file.size / 1024)} KB</span>
                    <button type="button" class="remove-file" data-index="${index}">Remove</button>
                `;
                fileList.appendChild(row);
            });
        }

        fileInput?.addEventListener('change', renderFiles);

        fileList?.addEventListener('click', function (event) {
            const button = event.target.closest('.remove-file');
            if (!button) return;

            const dt = new DataTransfer();
            Array.from(fileInput.files)
                .filter((_, index) => index !== Number(button.dataset.index))
                .forEach((file) => dt.items.add(file));
            fileInput.files = dt.files;
            renderFiles();
        });

        ['dragenter', 'dragover'].forEach((eventName) => {
            dropZone?.addEventListener(eventName, (event) => {
                event.preventDefault();
                dropZone.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach((eventName) => {
            dropZone?.addEventListener(eventName, (event) => {
                event.preventDefault();
                dropZone.classList.remove('dragover');
            });
        });

        dropZone?.addEventListener('drop', (event) => {
            const dt = new DataTransfer();
            Array.from(event.dataTransfer.files).forEach((file) => dt.items.add(file));
            fileInput.files = dt.files;
            renderFiles();
        });
    </script>
</body>
</html>
