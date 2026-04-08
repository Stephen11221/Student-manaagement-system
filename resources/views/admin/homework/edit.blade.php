<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Homework | {{ config('app.name', 'School Portal') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
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
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        }
        h1 {
            color: #f8fafc;
            font-size: 2rem;
            margin-bottom: 8px;
        }
        .subtitle {
            color: #94a3b8;
        }
        .card {
            background: rgba(15, 23, 42, 0.78);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 16px;
            padding: 30px;
            backdrop-filter: blur(18px);
        }
        .form-section {
            margin-bottom: 28px;
        }
        .section-title {
            color: #22d3ee;
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(34, 211, 238, 0.2);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #dbeafe;
            font-weight: 600;
            font-size: 0.95rem;
        }
        input[type="text"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 8px;
            background: rgba(2, 6, 23, 0.56);
            color: #f8fafc;
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #22d3ee;
            background: rgba(2, 6, 23, 0.78);
            box-shadow: 0 0 0 3px rgba(34, 211, 238, 0.1);
        }
        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2322d3ee' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
        }
        textarea {
            resize: vertical;
            min-height: 120px;
        }
        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid rgba(148, 163, 184, 0.1);
        }
        .btn {
            flex: 1;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
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
        }
        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.2);
        }
        .error-message {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 8px;
            padding: 12px;
            color: #ef4444;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .error-text {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 4px;
        }
        .help-text {
            color: #94a3b8;
            font-size: 0.8rem;
            margin-top: 4px;
        }
        .info-box {
            background: rgba(34, 211, 238, 0.08);
            border-left: 4px solid #22d3ee;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
                <div>
                    <h1><i class="fas fa-edit"></i> Edit Homework</h1>
                    <p class="subtitle">Update assignment details and settings</p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </header>

        <div class="card">
            @if ($errors->any())
                <div class="error-message">
                    <strong><i class="fas fa-exclamation-circle"></i> Please fix the errors:</strong>
                    <ul style="margin-top: 8px; margin-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="info-box">
                <i class="fas fa-info-circle"></i> Submissions: <strong>{{ $homework->submissions()->count() }}</strong> students have submitted
            </div>

            <form method="POST" action="{{ route('admin.homework.update', $homework->id) }}">
                @csrf
                @method('PUT')

                <div class="form-section">
                    <div class="section-title"><i class="fas fa-info-circle"></i> Assignment Details</div>

                    <div class="form-group">
                        <label for="title"><i class="fas fa-heading"></i> Homework Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title', $homework->title) }}" placeholder="e.g., Chapter 5 Exercises" required>
                        @error('title')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description"><i class="fas fa-file-alt"></i> Description</label>
                        <textarea id="description" name="description" placeholder="Provide detailed instructions for the homework assignment..." required>{{ old('description', $homework->description) }}</textarea>
                        @error('description')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-title"><i class="fas fa-cogs"></i> Assignment Settings</div>

                    <div class="form-group">
                        <label for="class_id"><i class="fas fa-chalkboard"></i> Select Class</label>
                        <select id="class_id" name="class_id" required>
                            <option value="">-- Choose a class --</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id', $homework->class_id) == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="submission_type"><i class="fas fa-file-upload"></i> Submission Type</label>
                        <select id="submission_type" name="submission_type" required>
                            <option value="written" {{ old('submission_type', $homework->submission_type) == 'written' ? 'selected' : '' }}>Written (Text)</option>
                            <option value="file" {{ in_array(old('submission_type', $homework->submission_type), ['file', 'upload'], true) ? 'selected' : '' }}>File Upload</option>
                        </select>
                        @error('submission_type')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="due_date"><i class="fas fa-calendar-alt"></i> Due Date</label>
                        <input type="date" id="due_date" name="due_date" value="{{ old('due_date', $homework->due_date?->format('Y-m-d')) }}">
                        @error('due_date')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="{{ route('admin.homework.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
