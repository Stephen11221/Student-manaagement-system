<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notifications | {{ config('app.name', 'School Portal') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:"Instrument Sans",sans-serif; background:linear-gradient(135deg,#020617 0%,#0f172a 54%,#111827 100%); color:#e2e8f0; min-height:100vh; }
        .container { max-width:900px; margin:0 auto; padding:40px 20px; }
        header,.header-actions,.filter-bar form,.notification-item { display:flex; }
        header { justify-content:space-between; align-items:center; margin-bottom:40px; padding-bottom:20px; border-bottom:1px solid rgba(148,163,184,.1); gap:16px; flex-wrap:wrap; }
        h1 { color:#f8fafc; font-size:2rem; }
        .header-actions { gap:10px; flex-wrap:wrap; }
        .btn,.back-btn,.filter-btn,.action-btn { border-radius:12px; cursor:pointer; text-decoration:none; transition:.2s; font-weight:700; }
        .back-btn,.btn,.action-btn { display:inline-flex; align-items:center; gap:8px; }
        .back-btn,.btn,.action-btn { padding:11px 16px; }
        .back-btn,.action-btn { background:rgba(34,211,238,.1); color:#22d3ee; border:1px solid rgba(34,211,238,.3); }
        .btn { background:linear-gradient(135deg,#22d3ee,#06b6d4); color:#082f49; border:none; }
        .filter-bar { margin-bottom:24px; }
        .filter-bar form { gap:10px; flex-wrap:wrap; }
        .filter-btn { padding:9px 16px; border:1px solid rgba(51,65,85,.95); background:rgba(15,23,42,.86); color:#cbd5e1; font-size:.85rem; }
        .filter-btn.active,.filter-btn:hover { background:rgba(34,211,238,.18); color:#e0f7ff; border-color:rgba(34,211,238,.45); }
        .stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:12px; margin-bottom:24px; }
        .stat-card,.notification-item,.empty-state { background:rgba(15,23,42,.9); border:1px solid rgba(51,65,85,.95); border-radius:16px; backdrop-filter:blur(18px); }
        .stat-card { padding:12px; text-align:center; }
        .stat-number { color:#67e8f9; font-size:1.5rem; font-weight:800; }
        .stat-label,.notification-time,.empty-text { color:#cbd5e1; }
        .notification-item { gap:16px; align-items:flex-start; padding:20px; margin-bottom:16px; }
        .notification-item.unread { border-left:4px solid #38bdf8; background:rgba(14,165,233,.12); }
        .notification-icon { font-size:1.2rem; flex-shrink:0; width:54px; height:54px; background:rgba(14,165,233,.16); border-radius:14px; display:flex; align-items:center; justify-content:center; color:#7dd3fc; }
        .notification-content { flex:1; }
        .notification-title { color:#f8fafc; font-weight:600; margin-bottom:4px; }
        .notification-message { color:#cbd5e1; font-size:.95rem; margin-bottom:8px; line-height:1.5; }
        .notification-actions { display:flex; gap:8px; flex-shrink:0; flex-wrap:wrap; }
        .action-btn.delete { background:rgba(239,68,68,.14); color:#fecaca; border-color:rgba(239,68,68,.32); }
        .empty-state { text-align:center; padding:60px 20px; }
        .empty-icon { font-size:3rem; color:#7dd3fc; margin-bottom:16px; }
        .badge { display:inline-block; padding:4px 10px; border-radius:4px; font-size:.75rem; font-weight:600; margin-left:8px; }
        .badge-homework { background:rgba(59,130,246,.22); color:#bfdbfe; }
        .badge-attendance { background:rgba(34,211,238,.22); color:#cffafe; }
        .badge-announcement { background:rgba(249,115,22,.22); color:#fed7aa; }
        .badge-grade { background:rgba(16,185,129,.22); color:#bbf7d0; }
        .badge-class { background:rgba(168,85,247,.22); color:#e9d5ff; }
        .badge-submission { background:rgba(250,204,21,.22); color:#fef08a; }
        .badge-exam { background:rgba(59,130,246,.2); color:#dbeafe; }
        .badge-message { background:rgba(34,211,238,.2); color:#cffafe; }
        .section-panel { margin-bottom: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fa-regular fa-bell"></i> Notifications</h1>
            <div class="header-actions">
                <a href="{{ route('dashboard') }}" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
                <form method="POST" action="{{ route('notifications.mark-all-as-read') }}">
                    @csrf
                    <button type="submit" class="btn"><i class="fa-solid fa-check-double"></i> Mark All Read</button>
                </form>
            </div>
        </header>

        <div class="section-panel">
            <x-announcement-message
                tone="info"
                icon="bell"
                label="Notification Center"
                title="Read updates as they arrive"
                body="Check new notifications first so you do not miss homework, attendance, exam, or school-wide announcements."
                :details="[
                    ['icon' => 'bullhorn', 'label' => 'Announcements', 'value' => 'School-wide notices and urgent updates'],
                    ['icon' => 'file-pen', 'label' => 'Homework', 'value' => 'Assignments and submission reminders'],
                    ['icon' => 'clipboard-check', 'label' => 'Attendance', 'value' => 'Daily check-in and class records'],
                    ['icon' => 'calendar-check', 'label' => 'Meetings', 'value' => 'Team and individual meeting invites'],
                    ['icon' => 'file-signature', 'label' => 'Exams', 'value' => 'Exam schedules and results'],
                ]"
                action="Open the newest notification and act on it before it drops off your list."
                deadline="Tip: The unread count shows what still needs attention"
                cta-label="Back to Dashboard"
                cta-href="{{ route('dashboard') }}"
            />
        </div>

        <div class="stats">
            <div class="stat-card"><div class="stat-number">{{ $notifications->total() }}</div><div class="stat-label">Total</div></div>
            <div class="stat-card"><div class="stat-number">{{ $unreadCount }}</div><div class="stat-label">Unread</div></div>
            <div class="stat-card"><div class="stat-number">{{ $submissionCount }}</div><div class="stat-label">Submissions</div></div>
            <div class="stat-card"><div class="stat-number">{{ $homeworkCount }}</div><div class="stat-label">Homework</div></div>
            <div class="stat-card"><div class="stat-number">{{ $attendanceCount }}</div><div class="stat-label">Attendance</div></div>
            <div class="stat-card"><div class="stat-number">{{ $meetingCount ?? 0 }}</div><div class="stat-label">Meetings</div></div>
            <div class="stat-card"><div class="stat-number">{{ $examCount ?? 0 }}</div><div class="stat-label">Exams</div></div>
            <div class="stat-card"><div class="stat-number">{{ $messageCount ?? 0 }}</div><div class="stat-label">Messages</div></div>
        </div>

        <div class="filter-bar">
            <form method="GET">
                <button type="submit" name="filter" value="" class="filter-btn {{ request('filter') === null || request('filter') === '' ? 'active' : '' }}"><i class="fa-solid fa-list"></i> All</button>
                <button type="submit" name="filter" value="unread" class="filter-btn {{ request('filter') === 'unread' ? 'active' : '' }}"><i class="fa-regular fa-circle"></i> Unread</button>
                <button type="submit" name="filter" value="submission" class="filter-btn {{ request('filter') === 'submission' ? 'active' : '' }}"><i class="fa-solid fa-cloud-arrow-up"></i> Uploads</button>
                <button type="submit" name="filter" value="homework" class="filter-btn {{ request('filter') === 'homework' ? 'active' : '' }}"><i class="fa-solid fa-file-pen"></i> Homework</button>
                <button type="submit" name="filter" value="attendance" class="filter-btn {{ request('filter') === 'attendance' ? 'active' : '' }}"><i class="fa-solid fa-clipboard-check"></i> Attendance</button>
                <button type="submit" name="filter" value="exam" class="filter-btn {{ request('filter') === 'exam' ? 'active' : '' }}"><i class="fa-solid fa-file-signature"></i> Exams</button>
                <button type="submit" name="filter" value="meeting" class="filter-btn {{ request('filter') === 'meeting' ? 'active' : '' }}"><i class="fa-solid fa-calendar-check"></i> Meetings</button>
                <button type="submit" name="filter" value="message" class="filter-btn {{ request('filter') === 'message' ? 'active' : '' }}"><i class="fa-solid fa-comments"></i> Messages</button>
            </form>
        </div>

        @if ($notifications->count() > 0)
            @foreach ($notifications as $notification)
                <div class="notification-item {{ is_null($notification->read_at) ? 'unread' : '' }}">
                    <div class="notification-icon">
                        @if ($notification->type === 'homework')
                            <i class="fa-solid fa-file-pen"></i>
                        @elseif ($notification->type === 'attendance')
                            <i class="fa-solid fa-clipboard-check"></i>
                        @elseif ($notification->type === 'grade')
                            <i class="fa-solid fa-graduation-cap"></i>
                        @elseif ($notification->type === 'class')
                            <i class="fa-solid fa-people-roof"></i>
                        @elseif ($notification->type === 'announcement')
                            <i class="fa-solid fa-bullhorn"></i>
                        @elseif ($notification->type === 'submission')
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        @elseif ($notification->type === 'exam')
                            <i class="fa-solid fa-file-signature"></i>
                        @elseif ($notification->type === 'meeting')
                            <i class="fa-solid fa-calendar-check"></i>
                        @elseif ($notification->type === 'message')
                            <i class="fa-solid fa-comments"></i>
                        @else
                            <i class="fa-solid fa-circle-info"></i>
                        @endif
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">
                            {{ $notification->title }}
                            @if ($notification->type === 'homework')
                                <span class="badge badge-homework">Homework</span>
                            @elseif ($notification->type === 'attendance')
                                <span class="badge badge-attendance">Attendance</span>
                            @elseif ($notification->type === 'grade')
                                <span class="badge badge-grade">Grade</span>
                            @elseif ($notification->type === 'class')
                                <span class="badge badge-class">Class</span>
                        @elseif ($notification->type === 'announcement')
                            <span class="badge badge-announcement">Announcement</span>
                        @elseif ($notification->type === 'submission')
                            <span class="badge badge-submission">Upload</span>
                        @elseif ($notification->type === 'exam')
                            <span class="badge badge-exam">Exam</span>
                        @elseif ($notification->type === 'meeting')
                            <span class="badge badge-message">Meeting</span>
                        @elseif ($notification->type === 'message')
                            <span class="badge badge-message">Message</span>
                        @endif
                        </div>
                        <div class="notification-message">{{ $notification->message }}</div>
                        <div class="notification-time"><i class="fa-regular fa-clock"></i> {{ $notification->created_at->diffForHumans() }}</div>
                        @if($notification->link)
                            <div style="margin-top:12px;">
                                <a href="{{ $notification->link }}" class="action-btn"><i class="fa-solid fa-arrow-up-right-from-square"></i> Open</a>
                            </div>
                        @endif
                    </div>
                    <div class="notification-actions">
                        @if (is_null($notification->read_at))
                            <form method="POST" action="{{ route('notifications.mark-as-read', $notification->id) }}">
                                @csrf
                                <button type="submit" class="action-btn"><i class="fa-solid fa-check"></i> Read</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('notifications.delete', $notification->id) }}" onsubmit="return confirm('Delete this notification?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn delete"><i class="fa-regular fa-trash-can"></i> Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <div class="empty-icon"><i class="fa-regular fa-bell-slash"></i></div>
                <div style="color:#f8fafc; font-size:1.3rem; margin-bottom:8px;">No notifications</div>
                <div class="empty-text">You're all caught up. Check back later for updates.</div>
            </div>
        @endif
    </div>

    @include('partials.idle-timeout-modal')
    <script src="{{ asset('js/idle-timeout.js') }}"></script>
    <script>
        document.documentElement.dataset.idleTimeout = "{{ config('idle.idle_timeout', 15) }}";
        document.documentElement.dataset.warningTime = "{{ config('idle.warning_time', 1) }}";
    </script>
</body>
</html>
