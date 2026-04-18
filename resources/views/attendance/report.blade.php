@extends('layouts.app')

@section('title', 'Attendance Report')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 lg:px-8">
    <div class="mb-8 flex flex-col gap-4 border-b border-slate-700/60 pb-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.24em] text-cyan-300">Reports</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-50">Attendance report</h1>
            <p class="mt-2 max-w-3xl text-slate-400">Printable summary for the current filter set. Use the CSV export for spreadsheet workflows.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.attendance.index', request()->query()) }}" class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-2 text-sm font-semibold text-slate-100 hover:border-cyan-400/40">Back to dashboard</a>
            <a href="{{ route('admin.attendance.export.csv', request()->query()) }}" class="rounded-xl bg-cyan-400 px-4 py-2 text-sm font-semibold text-cyan-950 hover:bg-cyan-300">Download CSV</a>
            <button onclick="window.print()" class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-2 text-sm font-semibold text-slate-100 hover:border-cyan-400/40">Print / Save PDF</button>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
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
            <div class="text-sm text-slate-400">Absent</div>
            <div class="mt-2 text-3xl font-bold text-rose-400">{{ $summary['absent'] }}</div>
        </div>
    </div>

    <div class="mt-6 overflow-x-auto rounded-3xl border border-slate-700/60 bg-slate-950/70">
        <table class="min-w-full divide-y divide-slate-800 text-sm">
            <thead class="bg-slate-900/90 text-slate-300">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold">Date</th>
                    <th class="px-4 py-3 text-left font-semibold">Scope</th>
                    <th class="px-4 py-3 text-left font-semibold">User</th>
                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                    <th class="px-4 py-3 text-left font-semibold">Check-in</th>
                    <th class="px-4 py-3 text-left font-semibold">Check-out</th>
                    <th class="px-4 py-3 text-left font-semibold">Remarks</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($records as $record)
                    <tr>
                        <td class="px-4 py-3 text-slate-100">{{ $record->attendance_date?->format('M d, Y') ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-300">{{ $record->scope_type === 'class' ? ($record->classRoom?->name ?? 'Class') : ($record->department?->name ?? 'Department') }}</td>
                        <td class="px-4 py-3 text-slate-100">{{ $record->student?->name ?? '-' }}</td>
                        <td class="px-4 py-3 font-semibold capitalize text-slate-100">{{ $record->status }}</td>
                        <td class="px-4 py-3 text-slate-300">{{ $record->check_in_at?->format('H:i') ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-300">{{ $record->check_out_at?->format('H:i') ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-300">{{ $record->remarks ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-400">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
