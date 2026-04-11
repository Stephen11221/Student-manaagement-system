<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Timetable</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-0: #020617;
            --bg-1: #0f172a;
            --bg-2: #111827;
            --panel: rgba(15, 23, 42, 0.84);
            --panel-soft: rgba(15, 23, 42, 0.62);
            --line: rgba(148, 163, 184, 0.18);
            --line-strong: rgba(34, 211, 238, 0.32);
            --text: #e2e8f0;
            --muted: #94a3b8;
            --heading: #f8fafc;
            --accent: #22d3ee;
            --accent-2: #06b6d4;
            --today: rgba(34, 211, 238, 0.15);
            --today-border: rgba(34, 211, 238, 0.4);
        }

        * { box-sizing: border-box; }
        body {
            font-family: "Instrument Sans", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(34, 211, 238, 0.12), transparent 28%),
                radial-gradient(circle at top right, rgba(59, 130, 246, 0.12), transparent 28%),
                linear-gradient(135deg, var(--bg-0), var(--bg-1) 54%, var(--bg-2) 100%);
            color: var(--text);
            margin: 0;
            min-height: 100vh;
        }

        .container {
            max-width: 1320px;
            margin: 0 auto;
            padding: 40px 20px 56px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
            gap: 12px;
            flex-wrap: wrap;
        }

        h1 {
            font-size: 2rem;
            color: var(--heading);
            margin: 0;
        }

        .subtitle {
            margin-top: 8px;
            color: var(--muted);
        }

        .toolbar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .btn {
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            color: #082f49;
            border: none;
            padding: 10px 18px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 10px 25px rgba(34, 211, 238, 0.14);
        }

        .btn-muted {
            background: rgba(34, 211, 238, 0.1);
            color: #22d3ee;
            border: 1px solid rgba(34, 211, 238, 0.3);
            box-shadow: none;
        }

        .hero {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.92), rgba(2, 6, 23, 0.72));
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 22px;
            margin-bottom: 18px;
            backdrop-filter: blur(18px);
        }

        .hero-top {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .hero-title {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--heading);
            font-size: 1.4rem;
            margin: 0;
        }

        .hero-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(34, 211, 238, 0.1);
            color: #c4f1ff;
            border: 1px solid rgba(34, 211, 238, 0.2);
            font-size: 0.84rem;
            font-weight: 700;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin-top: 18px;
        }

        .stat {
            background: rgba(2, 6, 23, 0.34);
            border: 1px solid rgba(148, 163, 184, 0.12);
            border-radius: 14px;
            padding: 14px;
        }

        .stat-label {
            color: var(--muted);
            font-size: 0.82rem;
            margin-bottom: 8px;
        }

        .stat-value {
            color: var(--heading);
            font-size: 1.2rem;
            font-weight: 700;
        }

        .board {
            background: rgba(15, 23, 42, 0.72);
            border: 1px solid var(--line);
            border-radius: 20px;
            overflow: hidden;
            backdrop-filter: blur(18px);
        }

        .board-header {
            display: grid;
            grid-template-columns: repeat(7, minmax(150px, 1fr));
            gap: 1px;
            background: rgba(148, 163, 184, 0.08);
        }

        .day-head {
            padding: 16px 14px;
            background: rgba(2, 6, 23, 0.56);
            min-height: 82px;
        }

        .day-name {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--heading);
            font-weight: 700;
            margin-bottom: 8px;
            gap: 8px;
        }

        .day-date {
            color: var(--muted);
            font-size: 0.82rem;
        }

        .day-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
            background: rgba(34, 211, 238, 0.12);
            color: #93c5fd;
        }

        .day-head.today {
            background: var(--today);
            box-shadow: inset 0 0 0 1px var(--today-border);
        }

        .board-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(150px, 1fr));
            gap: 1px;
            background: rgba(148, 163, 184, 0.08);
        }

        .day-column {
            min-height: 320px;
            background: rgba(2, 6, 23, 0.46);
            padding: 14px;
        }

        .day-column.today {
            background: rgba(34, 211, 238, 0.08);
        }

        .slot {
            position: relative;
            background: linear-gradient(180deg, rgba(34, 211, 238, 0.12), rgba(15, 23, 42, 0.84));
            border: 1px solid rgba(34, 211, 238, 0.22);
            border-radius: 16px;
            padding: 14px;
            margin-bottom: 12px;
            box-shadow: 0 10px 25px rgba(2, 6, 23, 0.22);
        }

        .slot:last-child {
            margin-bottom: 0;
        }

        .slot-time {
            color: #67e8f9;
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .slot-class {
            color: var(--heading);
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .slot-meta {
            color: var(--muted);
            font-size: 0.86rem;
            line-height: 1.45;
        }

        .slot-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .btn-meeting {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #22d3ee;
            color: #082f49;
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.88rem;
        }

        .empty-state {
            background: rgba(15, 23, 42, 0.78);
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 32px;
            text-align: center;
            color: var(--muted);
        }

        .empty-state i {
            font-size: 2rem;
            color: var(--accent);
            margin-bottom: 12px;
        }

        .mobile-list {
            display: none;
        }

        @media (max-width: 1100px) {
            .board-header,
            .board-grid {
                grid-template-columns: repeat(2, minmax(220px, 1fr));
            }
        }

        @media (max-width: 720px) {
            .board-header,
            .board-grid {
                display: none;
            }

            .mobile-list {
                display: grid;
                gap: 12px;
            }

            .mobile-day {
                background: rgba(15, 23, 42, 0.78);
                border: 1px solid var(--line);
                border-radius: 18px;
                padding: 16px;
            }

            .mobile-day.today {
                box-shadow: inset 0 0 0 1px var(--today-border);
                background: var(--today);
            }

            .mobile-day h3 {
                margin: 0 0 10px;
                color: var(--heading);
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    @php
        $dayOrder = [
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
            'Sunday' => 7,
        ];

        $weekDays = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday',
        ];

        $groupedSlots = collect($timetables ?? [])
            ->sortBy(fn ($slot) => sprintf('%02d-%s', $dayOrder[$slot->day_of_week] ?? 99, $slot->start_time))
            ->groupBy('day_of_week');

        $todayName = now()->format('l');
        $weekSlotsCount = collect($timetables ?? [])->count();
    @endphp

    <div class="container">
        <header>
            <div>
                <h1><i class="fa-regular fa-calendar"></i> My Timetable</h1>
                <div class="subtitle">Teams-style weekly view for your current class schedule.</div>
            </div>
            <div class="toolbar">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-muted"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
                @endauth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn" type="submit"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
                </form>
            </div>
        </header>

        <div class="hero">
            <div class="hero-top">
                <div>
                    <div class="hero-title">
                        <i class="fa-solid fa-diagram-project"></i>
                        <span>{{ $classes->first()?->name ?? 'Current Class' }}</span>
                    </div>
                    <div class="hero-meta">
                        <span class="pill"><i class="fa-solid fa-chalkboard-user"></i> Trainer: {{ $classes->first()?->trainer?->name ?? 'Not assigned' }}</span>
                        <span class="pill"><i class="fa-solid fa-calendar-days"></i> {{ $weekSlotsCount }} scheduled slot{{ $weekSlotsCount === 1 ? '' : 's' }}</span>
                        <span class="pill"><i class="fa-solid fa-bolt"></i> Today: {{ $todayName }}</span>
                    </div>
                </div>
                <div class="pill">
                    <i class="fa-solid fa-info-circle"></i>
                    Microsoft Teams-style weekly board
                </div>
            </div>

            <div class="stats">
                <div class="stat">
                    <div class="stat-label">Week Coverage</div>
                    <div class="stat-value">{{ $groupedSlots->count() }} day{{ $groupedSlots->count() === 1 ? '' : 's' }}</div>
                </div>
                <div class="stat">
                    <div class="stat-label">Current Mode</div>
                    <div class="stat-value">{{ $classes->first()?->delivery_mode === 'online' ? 'Online' : 'Physical' }}</div>
                </div>
                <div class="stat">
                    <div class="stat-label">Room</div>
                    <div class="stat-value">{{ $classes->first()?->room_number ?: 'Not set' }}</div>
                </div>
                <div class="stat">
                    <div class="stat-label">Next Step</div>
                    <div class="stat-value">{{ $groupedSlots->count() ? 'Check your next slot' : 'No sessions yet' }}</div>
                </div>
            </div>
        </div>

        @if ($timetables->count() > 0)
            <div class="board">
                <div class="board-header">
                    @foreach ($weekDays as $day)
                        <div class="day-head {{ $todayName === $day ? 'today' : '' }}">
                            <div class="day-name">
                                <span>{{ $day }}</span>
                                @if ($todayName === $day)
                                    <span class="day-badge"><i class="fa-solid fa-circle"></i> Today</span>
                                @endif
                            </div>
                            <div class="day-date">
                                {{ $groupedSlots->get($day)?->count() ?? 0 }} session{{ (($groupedSlots->get($day)?->count() ?? 0) === 1) ? '' : 's' }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="board-grid">
                    @foreach ($weekDays as $day)
                        <div class="day-column {{ $todayName === $day ? 'today' : '' }}">
                            @forelse ($groupedSlots->get($day, collect()) as $slot)
                                <div class="slot">
                                    <div class="slot-time">{{ $slot->time_range }}</div>
                                    <div class="slot-class">{{ $slot->class?->name ?? 'Class unavailable' }}</div>
                                    <div class="slot-meta">
                                        <div>{{ $slot->topic ? $slot->topic : 'Topic will be shared by your trainer.' }}</div>
                                        <div style="margin-top:6px;">
                                            <i class="fa-solid fa-chalkboard-user"></i>
                                            {{ $slot->class?->trainer?->name ?? 'Trainer not assigned' }}
                                        </div>
                                    </div>
                                    <div class="slot-actions">
                                        @if ($slot->meeting_link)
                                            <a href="{{ $slot->meeting_link }}" target="_blank" class="btn-meeting"><i class="fa-solid fa-video"></i> Join</a>
                                        @endif
                                        <span class="pill" style="padding:6px 10px;font-size:.76rem;">
                                            <i class="fa-regular fa-clock"></i> {{ $slot->start_time }} - {{ $slot->end_time }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="slot" style="opacity:.72;border-style:dashed;">
                                    <div class="slot-time">No class</div>
                                    <div class="slot-meta">No sessions scheduled for this day.</div>
                                </div>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mobile-list" style="margin-top:16px;">
                @foreach ($weekDays as $day)
                    <div class="mobile-day {{ $todayName === $day ? 'today' : '' }}">
                        <h3>
                            <i class="fa-solid fa-calendar-day"></i> {{ $day }}
                            @if ($todayName === $day)
                                <span class="day-badge" style="margin-left:8px;"><i class="fa-solid fa-circle"></i> Today</span>
                            @endif
                        </h3>
                        @forelse ($groupedSlots->get($day, collect()) as $slot)
                            <div class="slot">
                                <div class="slot-time">{{ $slot->time_range }}</div>
                                <div class="slot-class">{{ $slot->class?->name ?? 'Class unavailable' }}</div>
                                <div class="slot-meta">{{ $slot->topic ?: 'Topic will be shared by your trainer.' }}</div>
                                @if ($slot->meeting_link)
                                    <div class="slot-actions">
                                        <a href="{{ $slot->meeting_link }}" target="_blank" class="btn-meeting"><i class="fa-solid fa-video"></i> Join</a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="slot" style="opacity:.72;border-style:dashed;">
                                <div class="slot-time">No class</div>
                                <div class="slot-meta">No sessions scheduled for this day.</div>
                            </div>
                        @endforelse
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div><i class="fa-regular fa-calendar-xmark"></i></div>
                <h2 style="margin:0 0 10px;color:#f8fafc;">No timetable available yet</h2>
                <p style="margin:0;">Please enroll in a class or ask your trainer to add timetable sessions.</p>
            </div>
        @endif
    </div>
</body>
</html>
