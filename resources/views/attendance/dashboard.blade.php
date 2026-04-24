@extends('layouts.app')

@section('title', 'Attendance Dashboard')

@section('content')
@php
    $scopeLabel = function ($record) {
        return $record->scope_type === 'class'
            ? ($record->classRoom?->name ?? 'Class')
            : ($record->department?->name ?? 'Department');
    };

    $statusMeta = [
        'present' => ['label' => 'Present', 'icon' => 'user-check', 'class' => 'status-success'],
        'late' => ['label' => 'Late', 'icon' => 'user-clock', 'class' => 'status-warning'],
        'excused' => ['label' => 'Excused', 'icon' => 'user-shield', 'class' => 'status-info'],
        'absent' => ['label' => 'Absent', 'icon' => 'user-times', 'class' => 'status-danger'],
    ];
@endphp

<style>
    :root {
        --page-bg: #020617;
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
        --info: #06b6d4;
    }

    body {
        background:
            radial-gradient(circle at top left, rgba(56, 189, 248, 0.14), transparent 24%),
            radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.1), transparent 22%),
            linear-gradient(135deg, #020617 0%, #0f172a 56%, #111827 100%);
        color: var(--text);
    }

    .page-shell {
        width: min(1440px, calc(100% - 32px));
        margin: 0 auto;
        padding: 40px 0 48px;
    }

    .page-header {
        display: grid;
        gap: 20px;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: end;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 24px;
    }

    .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        border: 1px solid rgba(56, 189, 248, 0.28);
        background: rgba(56, 189, 248, 0.12);
        color: #cffafe;
        padding: 8px 12px;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.18em;
        text-transform: uppercase;
    }

    h1, h2, p { margin: 0; }

    .page-title {
        margin-top: 12px;
        font-size: clamp(2.1rem, 3.5vw, 3.25rem);
        line-height: 1.05;
        font-weight: 800;
        color: var(--text);
    }

    .page-copy {
        margin-top: 12px;
        max-width: 72ch;
        color: var(--muted);
        line-height: 1.7;
    }

    .action-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 16px;
        padding: 12px 16px;
        border: 1px solid transparent;
        text-decoration: none;
        font-weight: 800;
        transition: transform 160ms ease, background 160ms ease, border-color 160ms ease;
    }

    .btn:hover { transform: translateY(-1px); }
    .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-strong)); color: #082f49; }
    .btn-secondary { background: rgba(15, 23, 42, 0.92); color: var(--text); border-color: var(--border); }

    .surface {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 28px;
        box-shadow: 0 24px 80px rgba(2, 6, 23, 0.34);
        backdrop-filter: blur(18px);
    }

    .grid-5 {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(5, minmax(0, 1fr));
    }

    .stat-card {
        padding: 20px;
        border-radius: 24px;
        background: var(--surface-strong);
        border: 1px solid var(--border);
    }

    .stat-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .stat-value {
        margin-top: 12px;
        font-size: clamp(1.9rem, 3vw, 2.8rem);
        line-height: 1;
        font-weight: 800;
        color: var(--text);
    }

    .stat-helper {
        margin-top: 10px;
        color: var(--muted);
        line-height: 1.6;
    }

    .stat-value.success { color: #86efac; }
    .stat-value.warning { color: #fcd34d; }
    .stat-value.info { color: #67e8f9; }
    .stat-value.danger { color: #fda4af; }

    .layout {
        margin-top: 24px;
        display: grid;
        gap: 24px;
        grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
    }

    .section {
        padding: 24px;
    }

    .section + .section { margin-top: 24px; }

    .section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--text);
    }

    .section-subtitle {
        margin-top: 6px;
        color: var(--muted);
    }

    .filters {
        display: grid;
        gap: 14px;
        grid-template-columns: repeat(6, minmax(0, 1fr));
    }

    .field {
        display: grid;
        gap: 8px;
    }

    .field label {
        color: #e2e8f0;
        font-size: 0.9rem;
        font-weight: 700;
    }

    .field input,
    .field select {
        width: 100%;
        border-radius: 14px;
        border: 1px solid var(--border);
        background: rgba(2, 6, 23, 0.62);
        color: var(--text);
        padding: 12px 14px;
        font: inherit;
        outline: none;
    }

    .field input:focus,
    .field select:focus {
        border-color: rgba(56, 189, 248, 0.65);
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.12);
    }

    .filters .field.date { grid-column: span 1; }
    .filters .field.status { grid-column: span 1; }
    .filters .field.full { grid-column: span 2; }
    .filters .submit { grid-column: 1 / -1; }

    .chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 7px 12px;
        font-size: 0.78rem;
        font-weight: 800;
        border: 1px solid transparent;
    }

    .status-success { background: rgba(34, 197, 94, 0.14); color: #bbf7d0; border-color: rgba(34, 197, 94, 0.3); }
    .status-warning { background: rgba(245, 158, 11, 0.14); color: #fde68a; border-color: rgba(245, 158, 11, 0.3); }
    .status-info { background: rgba(6, 182, 212, 0.14); color: #cffafe; border-color: rgba(6, 182, 212, 0.3); }
    .status-danger { background: rgba(239, 68, 68, 0.14); color: #fecaca; border-color: rgba(239, 68, 68, 0.3); }

    .calendar-grid {
        display: grid;
        gap: 14px;
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .calendar-day,
    .student-card,
    .table-wrap {
        background: rgba(2, 6, 23, 0.4);
        border: 1px solid rgba(51, 65, 85, 0.95);
        border-radius: 22px;
    }

    .calendar-day {
        padding: 16px;
    }

    .day-label {
        color: var(--muted);
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.14em;
    }

    .day-number {
        margin-top: 8px;
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text);
    }

    .day-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        margin-top: 14px;
        color: #e2e8f0;
        font-size: 0.8rem;
    }

    .day-meta span {
        border-radius: 10px;
        padding: 6px 8px;
        background: rgba(15, 23, 42, 0.78);
        border: 1px solid rgba(51, 65, 85, 0.9);
    }

    .student-card {
        padding: 16px;
    }

    .student-name {
        font-weight: 800;
        color: var(--text);
    }

    .student-email {
        margin-top: 4px;
        color: var(--muted);
        font-size: 0.9rem;
    }

    .table-wrap {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 980px;
    }

    thead {
        background: rgba(15, 23, 42, 0.95);
        color: #cbd5e1;
    }

    th, td {
        padding: 14px 16px;
        border-bottom: 1px solid rgba(51, 65, 85, 0.9);
        text-align: left;
        white-space: nowrap;
    }

    tbody td {
        color: #e2e8f0;
    }

    .status-cell {
        font-weight: 800;
        text-transform: capitalize;
    }

    .pagination-wrap {
        margin-top: 16px;
    }

    .notice {
        padding: 18px;
        border-radius: 22px;
        background: rgba(6, 182, 212, 0.1);
        border: 1px solid rgba(6, 182, 212, 0.28);
    }

    @media (max-width: 1200px) {
        .grid-5 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .layout { grid-template-columns: 1fr; }
        .calendar-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 720px) {
        .page-shell { width: calc(100% - 24px); padding-top: 24px; }
        .page-header { grid-template-columns: 1fr; }
        .action-row { justify-content: flex-start; }
        .grid-5 { grid-template-columns: 1fr; }
        .calendar-grid { grid-template-columns: 1fr; }
        .section { padding: 18px; }
    }
</style>

<div class="page-shell">
    <header class="page-header">
        <div>
            <p class="eyebrow"><i class="fa-solid fa-user-check"></i> Attendance</p>
            <h1 class="page-title">Attendance management dashboard</h1>
            <p class="page-copy">Track daily attendance, check-ins, check-outs, and class trends in one place. Filters are always visible, and status counts are designed to scan quickly on mobile and desktop.</p>
        </div>
        <div class="action-row">
            <a href="{{ route('admin.attendance.report', request()->query()) }}" class="btn btn-secondary">
                <i class="fa-regular fa-file-lines"></i> Printable report
            </a>
            <a href="{{ route('admin.attendance.export.csv', request()->query()) }}" class="btn btn-primary">
                <i class="fa-solid fa-file-arrow-down"></i> Export CSV
            </a>
        </div>
    </header>

    <section class="surface section">
        <div class="section-head">
            <div>
                <div class="section-title"><i class="fa-solid fa-filter"></i> Filters</div>
                <div class="section-subtitle">Filter by date, class, department, or status.</div>
            </div>
        </div>
        <form method="GET" class="filters">
            <label class="field date">
                <span>Date</span>
                <input type="date" name="date" value="{{ $filters['date'] ?? '' }}">
            </label>
            <label class="field date">
                <span>From</span>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}">
            </label>
            <label class="field date">
                <span>To</span>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}">
            </label>
            <label class="field full">
                <span>Class</span>
                <select name="class_id">
                    <option value="">All classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected(($filters['class_id'] ?? null) == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="field full">
                <span>Department</span>
                <select name="department_id">
                    <option value="">All departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" @selected(($filters['department_id'] ?? null) == $department->id)>{{ $department->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="field status">
                <span>Status</span>
                <select name="status">
                    <option value="">All statuses</option>
                    @foreach(['present','absent','late','excused'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? null) === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </label>
            <div class="submit">
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                    <i class="fa-solid fa-check"></i> Apply filters
                </button>
            </div>
        </form>
    </section>

    <div class="grid-5" style="margin-top: 24px;">
        <div class="stat-card">
            <div class="stat-label"><i class="fa-solid fa-chart-column"></i> Attendance rate</div>
            <div class="stat-value info">{{ $summary['attendanceRate'] }}%</div>
            <div class="stat-helper">Current attendance snapshot for the selected filters.</div>
        </div>
        <div class="stat-card">
            <div class="stat-label"><i class="fa-solid fa-user-check"></i> Present</div>
            <div class="stat-value success">{{ $summary['present'] }}</div>
            <div class="stat-helper">Students marked present.</div>
        </div>
        <div class="stat-card">
            <div class="stat-label"><i class="fa-solid fa-user-clock"></i> Late</div>
            <div class="stat-value warning">{{ $summary['late'] }}</div>
            <div class="stat-helper">Students who arrived late.</div>
        </div>
        <div class="stat-card">
            <div class="stat-label"><i class="fa-solid fa-user-shield"></i> Excused</div>
            <div class="stat-value" style="color:#c4b5fd;">{{ $summary['excused'] }}</div>
            <div class="stat-helper">Approved absences or exceptions.</div>
        </div>
        <div class="stat-card">
            <div class="stat-label"><i class="fa-solid fa-user-times"></i> Absent</div>
            <div class="stat-value danger">{{ $summary['absent'] }}</div>
            <div class="stat-helper">Students not present.</div>
        </div>
    </div>

    <div class="layout">
        <section class="surface section">
            <div class="section-head">
                <div>
                    <div class="section-title"><i class="fa-regular fa-calendar-days"></i> Calendar view</div>
                    <div class="section-subtitle">{{ $calendarMonth->format('F Y') }}</div>
                </div>
            </div>
            <div class="calendar-grid">
                @foreach($calendar as $day)
                    <article class="calendar-day">
                        <div class="day-label">{{ $day['label'] }}</div>
                        <div class="day-number">{{ \Illuminate\Support\Carbon::parse($day['date'])->format('d') }}</div>
                        <div class="day-meta">
                            <span class="status-success">P: {{ $day['present'] }}</span>
                            <span class="status-warning">L: {{ $day['late'] }}</span>
                            <span class="status-info">E: {{ $day['excused'] }}</span>
                            <span class="status-danger">A: {{ $day['absent'] }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="surface section">
            <div class="section-head">
                <div>
                    <div class="section-title"><i class="fa-solid fa-triangle-exclamation"></i> Absentees</div>
                    <div class="section-subtitle">Students currently marked absent.</div>
                </div>
            </div>
            <div style="display:grid; gap:12px;">
                @forelse($summary['absentees'] as $student)
                    <article class="student-card">
                        <div class="student-name">{{ $student->name }}</div>
                        <div class="student-email">{{ $student->email }}</div>
                        <div style="margin-top:10px;">
                            <span class="chip status-danger"><i class="fa-solid fa-user-times"></i> Absent</span>
                        </div>
                    </article>
                @empty
                    <div class="notice">
                        <div class="section-title"><i class="fa-solid fa-circle-check"></i> No absentees</div>
                        <div class="section-subtitle">No absentees were found for the selected filters.</div>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <section class="surface section" style="margin-top: 24px;">
        <div class="section-head">
            <div>
                <div class="section-title"><i class="fa-solid fa-table"></i> Attendance records</div>
                <div class="section-subtitle">{{ $records->total() }} records</div>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Scope</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        @php($meta = $statusMeta[$record->status] ?? ['label' => ucfirst($record->status), 'icon' => 'circle-info', 'class' => 'status-info'])
                        <tr>
                            <td>{{ $record->attendance_date?->format('M d, Y') ?? '-' }}</td>
                            <td>{{ $scopeLabel($record) }}</td>
                            <td>{{ $record->student?->name ?? '-' }}</td>
                            <td>
                                <span class="chip {{ $meta['class'] }}">
                                    <i class="fa-solid fa-{{ $meta['icon'] }}"></i> {{ $meta['label'] }}
                                </span>
                            </td>
                            <td>{{ $record->check_in_at?->format('H:i') ?? '-' }}</td>
                            <td>{{ $record->check_out_at?->format('H:i') ?? '-' }}</td>
                            <td>{{ $record->recordedBy?->name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 24px; text-align:center; color: var(--muted);">No records found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">{{ $records->links() }}</div>
    </section>
</div>
@endsection
