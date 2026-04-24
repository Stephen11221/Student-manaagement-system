@extends('layouts.app')

@section('title', 'Bulk Attendance')

@section('content')
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
        font-size: clamp(2rem, 3.4vw, 3rem);
        line-height: 1.05;
        font-weight: 800;
        color: var(--text);
    }

    .page-copy { margin-top: 12px; max-width: 72ch; color: var(--muted); line-height: 1.7; }

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
        padding: 24px;
    }

    .layout {
        display: grid;
        gap: 24px;
        grid-template-columns: minmax(0, 1.15fr) minmax(320px, 0.85fr);
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--text);
    }

    .section-subtitle { margin-top: 6px; color: var(--muted); }

    .banner {
        border-radius: 24px;
        border: 1px solid rgba(6, 182, 212, 0.28);
        background: rgba(6, 182, 212, 0.1);
        padding: 18px;
        margin-bottom: 18px;
    }

    .banner.warning {
        border-color: rgba(245, 158, 11, 0.28);
        background: rgba(245, 158, 11, 0.1);
    }

    .banner-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        margin-top: 14px;
    }

    .stat {
        border-radius: 18px;
        padding: 14px;
        background: rgba(2, 6, 23, 0.4);
        border: 1px solid rgba(51, 65, 85, 0.95);
    }

    .stat-label {
        color: var(--muted);
        font-size: 0.78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.12em;
    }

    .stat-value {
        margin-top: 8px;
        color: var(--text);
        font-size: 1.45rem;
        font-weight: 800;
    }

    .controls {
        display: grid;
        gap: 14px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        margin-bottom: 18px;
    }

    .field {
        display: grid;
        gap: 8px;
    }

    .field span {
        color: #e2e8f0;
        font-size: 0.9rem;
        font-weight: 700;
    }

    .field input,
    .field select,
    .field textarea {
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
    .field select:focus,
    .field textarea:focus {
        border-color: rgba(56, 189, 248, 0.65);
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.12);
    }

    .table-wrap {
        overflow-x: auto;
        border-radius: 24px;
        border: 1px solid var(--border);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 860px;
    }

    thead {
        background: rgba(15, 23, 42, 0.95);
        color: #cbd5e1;
    }

    th, td {
        padding: 14px 16px;
        border-bottom: 1px solid rgba(51, 65, 85, 0.9);
        text-align: left;
    }

    tbody td { color: #e2e8f0; }

    .row-status {
        width: 100%;
        border-radius: 14px;
        border: 1px solid var(--border);
        background: rgba(2, 6, 23, 0.62);
        color: var(--text);
        padding: 12px 14px;
    }

    .row-meta {
        color: var(--muted);
        font-size: 0.9rem;
    }

    .chips {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 8px 12px;
        font-size: 0.8rem;
        font-weight: 800;
        border: 1px solid transparent;
    }

    .status-success { background: rgba(34, 197, 94, 0.14); color: #bbf7d0; border-color: rgba(34, 197, 94, 0.3); }
    .status-warning { background: rgba(245, 158, 11, 0.14); color: #fde68a; border-color: rgba(245, 158, 11, 0.3); }
    .status-info { background: rgba(6, 182, 212, 0.14); color: #cffafe; border-color: rgba(6, 182, 212, 0.3); }
    .status-danger { background: rgba(239, 68, 68, 0.14); color: #fecaca; border-color: rgba(239, 68, 68, 0.3); }

    .helper-panel {
        display: grid;
        gap: 14px;
    }

    .info-card {
        padding: 18px;
        border-radius: 22px;
        background: var(--surface-strong);
        border: 1px solid var(--border);
    }

    .info-title {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--text);
        font-weight: 800;
    }

    .info-copy {
        margin-top: 8px;
        color: var(--muted);
        line-height: 1.65;
    }

    .actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    @media (max-width: 1100px) {
        .page-header,
        .layout { grid-template-columns: 1fr; }
        .controls { grid-template-columns: 1fr; }
        .banner-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 720px) {
        .page-shell { width: calc(100% - 24px); padding-top: 24px; }
        .actions { width: 100%; }
        .btn { width: 100%; }
        .surface { padding: 18px; }
    }
</style>

<div class="page-shell">
    <header class="page-header">
        <div>
            <p class="eyebrow"><i class="fa-solid fa-user-check"></i> Bulk Attendance</p>
            <h1 class="page-title">{{ $scope->name }}</h1>
            <p class="page-copy">Mark attendance for everyone in this class with clear presets and a single save action. The screen uses the same spacing, cards, and status colors as the main attendance dashboard.</p>
        </div>
        <div class="actions">
            <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to dashboard
            </a>
        </div>
    </header>

    <form method="POST" action="{{ route('admin.attendance.bulk') }}" class="layout">
        @csrf
        <input type="hidden" name="scope_type" value="{{ $scopeType }}">
        <input type="hidden" name="scope_id" value="{{ $scope->id }}">

        <section class="surface">
            <div class="banner">
                <div class="section-title"><i class="fa-solid fa-clipboard-check"></i> Attendance entry</div>
                <div class="section-subtitle">Use the preset controls to update the whole class at once, then fine-tune any student rows if needed.</div>
                <div class="banner-grid">
                    <div class="stat">
                        <div class="stat-label">Date</div>
                        <div class="stat-value">{{ request('date', now()->toDateString()) }}</div>
                    </div>
                    <div class="stat">
                        <div class="stat-label">Preset</div>
                        <div class="stat-value">Bulk</div>
                    </div>
                    <div class="stat">
                        <div class="stat-label">Rows</div>
                        <div class="stat-value">{{ $students->count() }}</div>
                    </div>
                </div>
            </div>

            <div class="controls">
                <label class="field">
                    <span>Attendance date</span>
                    <input type="date" name="attendance_date" value="{{ request('date', now()->toDateString()) }}">
                </label>
                <label class="field">
                    <span>Status preset</span>
                    <select id="bulkStatus">
                        <option value="present">Present</option>
                        <option value="late">Late</option>
                        <option value="excused">Excused</option>
                        <option value="absent">Absent</option>
                    </select>
                </label>
                <div class="field">
                    <span>Quick actions</span>
                    <button type="button" id="applyPreset" class="btn btn-primary">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Apply to all rows
                    </button>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr class="attendance-row" data-user-id="{{ $student->id }}">
                                <td>
                                    <div style="font-weight:800; color: var(--text);">{{ $student->name }}</div>
                                    <div class="row-meta">{{ $student->email }}</div>
                                </td>
                                <td>
                                    <select name="attendance[{{ $loop->index }}][status]" class="row-status">
                                        @foreach(['present','absent','late','excused'] as $status)
                                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="attendance[{{ $loop->index }}][user_id]" value="{{ $student->id }}">
                                </td>
                                <td>
                                    <input type="text" name="attendance[{{ $loop->index }}][remarks]" class="row-status" placeholder="Optional remark">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Save attendance
                </button>
                <a href="{{ route('admin.attendance.report') }}" class="btn btn-secondary">
                    <i class="fa-regular fa-file-lines"></i> Open report
                </a>
            </div>
        </section>

        <aside class="helper-panel">
            <div class="info-card">
                <div class="info-title"><i class="fa-solid fa-user-check"></i> Status guide</div>
                <div class="info-copy">Use Present for students in class, Late for delayed arrivals, Excused for approved absences, and Absent when the learner did not attend.</div>
            </div>
            <div class="info-card">
                <div class="info-title"><i class="fa-solid fa-triangle-exclamation"></i> Important</div>
                <div class="info-copy">Apply a preset first, then review individual rows before saving. That keeps the class record accurate and reduces accidental mistakes.</div>
            </div>
            <div class="info-card">
                <div class="info-title"><i class="fa-solid fa-shield-halved"></i> Duplicate protection</div>
                <div class="info-copy">Attendance is protected for the same day, so a student will not be recorded twice by accident.</div>
            </div>
        </aside>
    </form>
</div>

<script>
    document.getElementById('applyPreset')?.addEventListener('click', function () {
        const status = document.getElementById('bulkStatus').value;
        document.querySelectorAll('.row-status').forEach((select) => {
            if (select.tagName === 'SELECT') {
                select.value = status;
            }
        });
    });
</script>
@endsection
