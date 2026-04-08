<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Submit Homework | {{ config('app.name', 'School Portal') }}</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: "Instrument Sans", sans-serif;
            background: linear-gradient(135deg, #020617 0%, #0f172a 54%, #111827 100%);
            color: #e2e8f0;
            min-height: 100vh;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        }
        h1 {
            color: #f8fafc;
            font-size: 1.8rem;
        }
        .back-btn {
            background: rgba(34, 211, 238, 0.1);
            color: #22d3ee;
            border: 1px solid rgba(34, 211, 238, 0.3);
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }
        .back-btn:hover {
            background: rgba(34, 211, 238, 0.2);
        }
        .card {
            background: rgba(15, 23, 42, 0.78);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 16px;
            padding: 28px;
            backdrop-filter: blur(18px);
        }
        .assignment-info {
            background: rgba(34, 211, 238, 0.08);
            border-left: 4px solid #22d3ee;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
        }
        .assignment-title {
            color: #f8fafc;
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .assignment-desc {
            color: #cbd5e1;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 8px;
        }
        .assignment-meta {
            color: #94a3b8;
            font-size: 0.85rem;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .form-section {
            margin-bottom: 24px;
        }
        .section-title {
            color: #22d3ee;
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #dbeafe;
            font-weight: 600;
            font-size: 0.95rem;
        }
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 8px;
            background: rgba(2, 6, 23, 0.56);
            color: #f8fafc;
            font-family: inherit;
            font-size: 0.95rem;
            resize: vertical;
            min-height: 200px;
            transition: all 0.2s;
        }
        textarea:focus {
            outline: none;
            border-color: #22d3ee;
            background: rgba(2, 6, 23, 0.78);
            box-shadow: 0 0 0 3px rgba(34, 211, 238, 0.1);
        }
        .file-upload-box {
            position: relative;
            border: 2px dashed rgba(34, 211, 238, 0.3);
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: rgba(34, 211, 238, 0.05);
        }
        .file-upload-box:hover {
            border-color: #22d3ee;
            background: rgba(34, 211, 238, 0.1);
        }
        .file-upload-box.dragover {
            border-color: #22d3ee;
            background: rgba(34, 211, 238, 0.15);
        }
        #file-input {
            display: none;
        }
        .upload-icon {
            font-size: 2.5rem;
            margin-bottom: 12px;
        }
        .upload-text {
            color: #e2e8f0;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .upload-hint {
            color: #94a3b8;
            font-size: 0.85rem;
        }
        .file-list {
            margin-top: 16px;
        }
        .file-item {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 8px;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .file-name {
            color: #f8fafc;
            font-weight: 500;
            flex: 1;
        }
        .file-size {
            color: #94a3b8;
            font-size: 0.85rem;
            margin-right: 12px;
        }
        .remove-file {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .remove-file:hover {
            background: rgba(239, 68, 68, 0.2);
        }
        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid rgba(148, 163, 184, 0.1);
        }
        button {
            flex: 1;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #22d3ee, #06b6d4);
            color: #082f49;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(34, 211, 238, 0.2);
        }
        .btn-secondary {
            background: rgba(148, 163, 184, 0.1);
            color: #94a3b8;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }
        .btn-secondary:hover {
            background: rgba(148, 163, 184, 0.15);
            border-color: rgba(148, 163, 184, 0.3);
        }
        .error-message {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 8px;
            padding: 12px;
            color: #ef4444;
            margin-bottom: 16px;
            font-size: 0.9rem;
        }
        .success-message {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 8px;
            padding: 12px;
            color: #10b981;
            margin-bottom: 16px;
            font-size: 0.9rem;
        }
        .submission-summary {
            background: rgba(15, 118, 110, 0.12);
            border: 1px solid rgba(45, 212, 191, 0.22);
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 24px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-top: 12px;
        }
        .summary-card {
            background: rgba(2, 6, 23, 0.38);
            border-radius: 10px;
            padding: 12px;
        }
        .summary-label {
            color: #94a3b8;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 6px;
        }
        .summary-value {
            color: #f8fafc;
            font-weight: 700;
        }
        .existing-content {
            margin-top: 16px;
            padding: 14px;
            border-radius: 10px;
            background: rgba(2, 6, 23, 0.56);
            border: 1px solid rgba(148, 163, 184, 0.18);
            color: #cbd5e1;
            white-space: pre-wrap;
            line-height: 1.55;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fa-solid fa-file-pen"></i> Submit: {{ $homework->title }}</h1>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">@auth<a href="{{ route('dashboard') }}" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>@endauth<a href="{{ route('student.homework.index') }}" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back</a></div>
        </header>

        <div class="card">
            <div class="assignment-info">
                <div class="assignment-title">{{ $homework->title }}</div>
                <div class="assignment-desc">{{ $homework->description }}</div>
                <div class="assignment-meta">
                    <span><i class="fa-regular fa-calendar"></i> Due: {{ $homework->due_date ? $homework->due_date->format('M d, Y') : 'No deadline' }}</span>
                    <span><i class="fa-solid fa-list-check"></i> Type: {{ ucfirst($homework->submission_type === 'upload' ? 'file' : $homework->submission_type) }}</span>
                    <span><i class="fa-solid fa-school"></i> Class: {{ $homework->class?->name ?? 'Unavailable' }}</span>
                    @if($homework->class?->trainer)
                        <span><i class="fa-solid fa-chalkboard-user"></i> Trainer: {{ $homework->class->trainer->name }}</span>
                    @endif
                </div>
            </div>

            @if ($submission)
                <div class="submission-summary">
                    <strong><i class="fa-solid fa-circle-info"></i> Current submission on file</strong>
                    <div class="summary-grid">
                        <div class="summary-card">
                            <div class="summary-label">Status</div>
                            <div class="summary-value">{{ ucfirst($submission->status ?? 'draft') }}</div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-label">Submitted</div>
                            <div class="summary-value">{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y H:i') : 'Not yet submitted' }}</div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-label">Marks</div>
                            <div class="summary-value">{{ $submission->marks !== null ? $submission->marks.'%' : 'Pending' }}</div>
                        </div>
                    </div>
                    @if ($submission->feedback)
                        <div class="existing-content" style="margin-top:12px;">
                            <strong style="display:block; margin-bottom:8px; color:#f8fafc;"><i class="fa-regular fa-message"></i> Trainer Feedback</strong>
                            {{ $submission->feedback }}
                        </div>
                    @endif
                    @if ($submission->content)
                        <div class="existing-content">
                            <strong style="display:block; margin-bottom:8px; color:#f8fafc;"><i class="fa-regular fa-file-lines"></i> Your saved answer</strong>
                            {{ $submission->content }}
                        </div>
                    @endif
                    @if ($submission->file_path)
                        <div style="margin-top:12px;">
                            <a href="{{ asset('storage/'.$submission->file_path) }}" class="back-btn" target="_blank">
                                <i class="fa-solid fa-download"></i> View current file
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('student.homework.store', $homework->id) }}" enctype="multipart/form-data">
                @csrf

                @if ($errors->any())
                    <div class="error-message">
                        <strong><i class="fa-solid fa-triangle-exclamation"></i> Please fix the errors:</strong>
                        <ul style="margin-top: 8px; margin-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($homework->submission_type === 'written')
                    <div class="form-section">
                        <div class="section-title"><i class="fa-regular fa-pen-to-square"></i> Your Answer</div>
                        <label for="content">Write your answer below:</label>
                        <textarea 
                            id="content"
                            name="content" 
                            placeholder="Enter your answer here. Be thorough and clear." 
                            required
                            rows="12"
                        >{{ old('content', $submission?->content) }}</textarea>
                        @error('content')
                            <div style="color: #ef4444; font-size: 0.85rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    <div class="form-section">
                        <div class="section-title"><i class="fa-solid fa-paperclip"></i> Upload File</div>
                        <label>Select or drag and drop a file:</label>
                        
                        <div class="file-upload-box" id="dropZone">
                            <div class="upload-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                            <div class="upload-text">Click to browse or drag and drop</div>
                            <div class="upload-hint">Supported: PDF, DOC, DOCX, XLS, XLSX, TXT, ZIP (Max {{ number_format(submissionUploadMaxKilobytes() / 1024, 0) }}MB)</div>
                        </div>

                        <input 
                            type="file" 
                            id="file-input" 
                            name="file" 
                            {{ $submission?->file_path ? '' : 'required' }}
                        >

                        <div class="file-list" id="fileList"></div>
                        @error('file')
                            <div style="color: #ef4444; font-size: 0.85rem; margin-top: 8px;">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="button-group">
                    <button type="submit" class="btn-primary"><i class="fa-solid fa-circle-check"></i> {{ $submission ? 'Update Submission' : 'Submit Assignment' }}</button>
                    <a href="{{ route('student.homework.index') }}" style="padding: 12px 24px; border-radius: 8px; background: rgba(148, 163, 184, 0.1); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.2); text-decoration: none; font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; justify-content: center; gap: 8px;"><i class="fa-solid fa-arrow-left"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        @if ($homework->submission_type !== 'written')
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('file-input');
        const fileList = document.getElementById('fileList');

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            fileInput.files = e.dataTransfer.files;
            updateFileList();
        });

        fileInput.addEventListener('change', updateFileList);

        function updateFileList() {
            fileList.innerHTML = '';
            if (fileInput.files.length === 0) return;

            for (let file of fileInput.files) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                fileItem.innerHTML = `
                    <span class="file-name"><i class="fa-regular fa-file"></i> ${file.name}</span>
                    <span class="file-size">${fileSize} MB</span>
                `;
                fileList.appendChild(fileItem);
            }
        }
        @endif
    </script>
</body>
</html>
