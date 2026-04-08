<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Class | {{ config('app.name', 'School Portal') }}</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Instrument Sans", sans-serif;
            color: #e2e8f0;
            background: linear-gradient(135deg, #020617, #0f172a 54%, #111827 100%);
        }

        .container {
            max-width: 720px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .card {
            background: rgba(15, 23, 42, 0.78);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 18px;
            padding: 28px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        h1 {
            margin: 0;
            color: #f8fafc;
        }

        .subtitle {
            margin-top: 8px;
            color: #94a3b8;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #dbeafe;
            font-weight: 600;
        }

        input,
        textarea {
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid rgba(148, 163, 184, 0.2);
            background: rgba(2, 6, 23, 0.56);
            color: #f8fafc;
            font-family: inherit;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 18px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-weight: 700;
        }

        .btn-primary {
            width: 100%;
            background: linear-gradient(135deg, #22d3ee, #06b6d4);
            color: #082f49;
        }

        .btn-secondary {
            background: rgba(34, 211, 238, 0.1);
            color: #22d3ee;
            border: 1px solid rgba(34, 211, 238, 0.28);
        }

        .error-box {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid rgba(239, 68, 68, 0.3);
            background: rgba(239, 68, 68, 0.12);
            color: #fecaca;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="topbar">
            <a href="{{ route('trainer.classes.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Classes
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
        </div>

        <div class="card">
            <h1><i class="fa-regular fa-pen-to-square"></i> Edit Class</h1>
            <p class="subtitle">Update the class name, room, and description.</p>

            @if ($errors->any())
                <div class="error-box">
                    <strong><i class="fa-solid fa-triangle-exclamation"></i> Please fix the errors below.</strong>
                </div>
            @endif

            <form method="POST" action="{{ route('trainer.classes.update', $class->id) }}">
                @csrf

                <label for="name">Class Name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $class->name) }}" required>

                <label for="room_number">Room Number</label>
                <input id="room_number" type="text" name="room_number" value="{{ old('room_number', $class->room_number) }}">

                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4">{{ old('description', $class->description) }}</textarea>

                <button class="btn btn-primary" type="submit">
                    <i class="fa-regular fa-floppy-disk"></i> Save Changes
                </button>
            </form>
        </div>
    </div>
</body>
</html>
