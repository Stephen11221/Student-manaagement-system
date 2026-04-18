@extends('layouts.app')

@section('title', 'Attendance Dashboard')

@section('content')
@php
    $scopeLabel = function ($record) {
        return $record->scope_type === 'class'
            ? ($record->classRoom?->name ?? 'Class')
            : ($record->department?->name ?? 'Department');
    };
@endphp
<div class="mx-auto max-w-7xl px-4 py-8 lg:px-8">
    <div class="mb-8 flex flex-col gap-4 border-b border-slate-700/60 pb-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.24em] text-cyan-300">Attendance</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-50">Attendance management dashboard</h1>
            <p class="mt-2 max-w-3xl text-slate-400">Track daily attendance, check-ins, check-outs, and organization-wide attendance trends.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.attendance.report', request()->query()) }}" class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-2 text-sm font-semibold text-slate-100 hover:border-cyan-400/40">Printable report</a>
            <a href="{{ route('admin.attendance.export.csv', request()->query()) }}" class="rounded-xl bg-cyan-400 px-4 py-2 text-sm font-semibold text-cyan-950 hover:bg-cyan-300">Export CSV</a>
        </div>
    </div>

    <form method="GET" class="grid gap-3 rounded-2xl border border-slate-700/60 bg-slate-950/70 p-4 md:grid-cols-2 xl:grid-cols-6">
        <input type="date" name="date" value="{{ $filters['date'] ?? '' }}" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-slate-100">
        <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-slate-100">
        <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-slate-100">
        <select name="class_id" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-slate-100">
            <option value="">All classes</option>
            @foreach($classes as $class)
                <option value="{{ $class->id }}" @selected(($filters['class_id'] ?? null) == $class->id)>{{ $class->name }}</option>
            @endforeach
        </select>
        <select name="department_id" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-slate-100">
            <option value="">All departments</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}" @selected(($filters['department_id'] ?? null) == $department->id)>{{ $department->name }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-slate-100">
            <option value="">All statuses</option>
            @foreach(['present','absent','late','excused'] as $status)
                <option value="{{ $status }}" @selected(($filters['status'] ?? null) === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-xl bg-cyan-400 px-4 py-3 font-semibold text-cyan-950 hover:bg-cyan-300 md:col-span-2 xl:col-span-6">Apply filters</button>
    </form>

    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border border-slate-700/60 bg-slate-950/70 p-5">
            <div class="text-sm text-slate-400">Attendance rate</div>
            <div class="mt-2 text-3xl font-bold text-slate-50">{{ $summary['attendanceRate'] }}%</div>
        </div>
        <div class="rounded-2xl border border-slate-700/60 bg-slate-950/70 p-5">
            <div class="text-sm text-slate-400">Present</div>
            <div class="mt-2 text-3xl font-bold text-emerald-400">{{ $summary['present'] }}</div>
        </div>
        <div class="rounded-2xl border border-slate-700/60 bg-slate-950/70 p-5">
            <div class="text-sm text-slate-400">Late</div>
            <div class="mt-2 text-3xl font-bold text-amber-400">{{ $summary['late'] }}</div>
        </div>
        <div class="rounded-2xl border border-slate-700/60 bg-slate-950/70 p-5">
            <div class="text-sm text-slate-400">Excused</div>
            <div class="mt-2 text-3xl font-bold text-violet-400">{{ $summary['excused'] }}</div>
        </div>
        <div class="rounded-2xl border border-slate-700/60 bg-slate-950/70 p-5">
            <div class="text-sm text-slate-400">Absent</div>
            <div class="mt-2 text-3xl font-bold text-rose-400">{{ $summary['absent'] }}</div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.1fr_.9fr]">
        <section class="rounded-3xl border border-slate-700/60 bg-slate-950/70 p-5">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-50">Calendar view</h2>
                <span class="text-sm text-slate-400">{{ $calendarMonth->format('F Y') }}</span>
            </div>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-4">
                @foreach($calendar as $day)
                    <article class="rounded-2xl border border-slate-700/60 bg-slate-900/70 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ $day['label'] }}</div>
                                <div class="mt-1 text-lg font-semibold text-slate-50">{{ \Illuminate\Support\Carbon::parse($day['date'])->format('d') }}</div>
                            </div>
                            <div class="rounded-full bg-cyan-400/10 px-3 py-1 text-xs font-bold text-cyan-300">{{ $day['count'] }}</div>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-2 text-xs text-slate-300">
                            <span class="rounded-lg bg-emerald-400/10 px-2 py-1">P: {{ $day['present'] }}</span>
                            <span class="rounded-lg bg-amber-400/10 px-2 py-1">L: {{ $day['late'] }}</span>
                            <span class="rounded-lg bg-violet-400/10 px-2 py-1">E: {{ $day['excused'] }}</span>
                            <span class="rounded-lg bg-rose-400/10 px-2 py-1">A: {{ $day['absent'] }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="rounded-3xl border border-slate-700/60 bg-slate-950/70 p-5">
            <h2 class="text-lg font-semibold text-slate-50">Absentees</h2>
            <div class="mt-4 space-y-3">
                @forelse($summary['absentees'] as $student)
                    <div class="flex items-center justify-between rounded-2xl border border-slate-700/60 bg-slate-900/70 px-4 py-3">
                        <div>
                            <div class="font-semibold text-slate-50">{{ $student->name }}</div>
                            <div class="text-sm text-slate-400">{{ $student->email }}</div>
                        </div>
                        <span class="rounded-full bg-rose-400/10 px-3 py-1 text-xs font-bold text-rose-300">Absent</span>
                    </div>
                @empty
                    <div class="rounded-2xl border border-slate-700/60 bg-slate-900/70 px-4 py-8 text-center text-slate-400">No absentees for the selected filters.</div>
                @endforelse
            </div>
        </section>
    </div>

    <section class="mt-6 rounded-3xl border border-slate-700/60 bg-slate-950/70 p-5">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-50">Attendance records</h2>
            <div class="text-sm text-slate-400">{{ $records->total() }} records</div>
        </div>
        <div class="overflow-x-auto rounded-2xl border border-slate-700/60">
            <table class="min-w-full divide-y divide-slate-700 text-sm">
                <thead class="bg-slate-900/90 text-slate-300">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Date</th>
                        <th class="px-4 py-3 text-left font-semibold">Scope</th>
                        <th class="px-4 py-3 text-left font-semibold">User</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Check In</th>
                        <th class="px-4 py-3 text-left font-semibold">Check Out</th>
                        <th class="px-4 py-3 text-left font-semibold">Recorded By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 bg-slate-950/70">
                    @forelse($records as $record)
                        <tr>
                            <td class="px-4 py-3 text-slate-100">{{ $record->attendance_date?->format('M d, Y') ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-300">{{ $scopeLabel($record) }}</td>
                            <td class="px-4 py-3 text-slate-100">{{ $record->student?->name ?? '-' }}</td>
                            <td class="px-4 py-3 font-semibold capitalize text-slate-100">{{ $record->status }}</td>
                            <td class="px-4 py-3 text-slate-300">{{ $record->check_in_at?->format('H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-300">{{ $record->check_out_at?->format('H:i') ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-300">{{ $record->recordedBy?->name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">No records found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $records->links() }}</div>
    </section>
</div>
@endsection
