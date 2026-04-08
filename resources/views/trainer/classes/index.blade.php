<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Classes | {{ config('app.name', 'School Portal') }}</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px 56px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 32px;
        }

        h1 {
            margin: 0;
            color: #f8fafc;
            font-size: 2.5rem;
        }

        .subtitle {
            margin-top: 8px;
            color: #94a3b8;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn,
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-weight: 700;
            text-decoration: none;
        }

        .btn-primary,
        .action-btn.primary {
            background: linear-gradient(135deg, #22d3ee, #06b6d4);
            color: #082f49;
        }

        .btn-secondary,
        .action-btn.secondary {
            background: rgba(34, 211, 238, 0.1);
            color: #22d3ee;
            border: 1px solid rgba(34, 211, 238, 0.28);
        }

        .action-btn.danger {
            background: rgba(239, 68, 68, 0.16);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .status-banner {
            margin-bottom: 24px;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid rgba(16, 185, 129, 0.3);
            background: rgba(16, 185, 129, 0.12);
            color: #86efac;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .class-card,
        .empty-state {
            background: rgba(15, 23, 42, 0.78);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-radius: 18px;
            padding: 22px;
        }

        .class-name {
            margin: 0 0 10px;
            color: #f8fafc;
            font-size: 1.35rem;
        }

        .class-description {
            min-height: 44px;
            color: #94a3b8;
            line-height: 1.5;
        }

        .meta {
            display: grid;
            gap: 8px;
            margin-top: 16px;
            color: #cbd5e1;
            font-size: 0.95rem;
        }

        .card-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .card-actions form {
            margin: 0;
        }

        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 2.5rem;
            color: #22d3ee;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div>
                <h1><i class="fa-solid fa-school"></i> My Classes</h1>
                <p class="subtitle">Manage your teaching spaces, rooms, and student groups from one place.</p>
            </div>

            <div class="actions">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
                </a>
                <a href="{{ route('trainer.classes.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> New Class
                </a>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button class="btn btn-primary" type="submit">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </form>
            </div>
        </header>

        @if (session('status'))
            <div class="status-banner">
                <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
            </div>
        @endif

        @if ($classes->count() > 0)
            <div class="grid">
                @foreach ($classes as $cls)
                    <article class="class-card">
                        <h2 class="class-name">{{ $cls->name }}</h2>
                        <div class="class-description">
                            {{ $cls->description ?: 'No class description yet.' }}
                        </div>

                        <div class="meta">
                            <div><i class="fa-solid fa-location-dot"></i> {{ $cls->room_number ?? 'Room not assigned' }}</div>
                            <div><i class="fa-solid fa-users"></i> {{ $cls->students_count ?? $cls->students->count() }} students</div>
                            <div><i class="fa-solid fa-circle-check"></i> {{ ucfirst($cls->status ?? 'active') }}</div>
                        </div>

                        <div class="card-actions">
                            <a class="action-btn primary" href="{{ route('trainer.classes.show', $cls->id) }}">Open</a>
                            <a class="action-btn secondary" href="{{ route('trainer.classes.edit', $cls->id) }}">Edit</a>
                            <form method="POST" action="{{ route('trainer.classes.delete', $cls->id) }}">
                                @csrf
                                <button type="submit" class="action-btn danger" onclick="return confirm('Delete this class?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fa-regular fa-folder-open"></i>
                <h2 style="margin: 0 0 8px; color: #f8fafc;">No classes yet</h2>
                <p style="margin: 0;">Create your first class to get started.</p>
            </div>
        @endif
    </div>
</body>
</html>
