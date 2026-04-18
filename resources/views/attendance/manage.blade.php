@extends('layouts.app')

@section('title', 'Bulk Attendance')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 lg:px-8">
    <div class="mb-8 flex flex-col gap-4 border-b border-slate-700/60 pb-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.24em] text-cyan-300">Bulk Entry</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-50">{{ $scope->name }}</h1>
            <p class="mt-2 max-w-3xl text-slate-400">Mark attendance for everyone in this class quickly, with duplicate prevention for the same day.</p>
        </div>
        <a href="{{ route('admin.attendance.index') }}" class="rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-2 text-sm font-semibold text-slate-100 hover:border-cyan-400/40">Back to dashboard</a>
    </div>

    <form method="POST" action="{{ route('admin.attendance.bulk') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="scope_type" value="{{ $scopeType }}">
        <input type="hidden" name="scope_id" value="{{ $scope->id }}">
        <div class="grid gap-4 rounded-2xl border border-slate-700/60 bg-slate-950/70 p-4 md:grid-cols-3">
            <label class="block">
                <span class="mb-2 block text-sm font-semibold text-slate-300">Attendance date</span>
                <input type="date" name="attendance_date" value="{{ request('date', now()->toDateString()) }}" class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-slate-100">
            </label>
            <label class="block">
                <span class="mb-2 block text-sm font-semibold text-slate-300">Status preset</span>
                <select id="bulkStatus" class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-slate-100">
                    <option value="present">Present</option>
                    <option value="late">Late</option>
                    <option value="excused">Excused</option>
                    <option value="absent">Absent</option>
                </select>
            </label>
            <label class="block">
                <span class="mb-2 block text-sm font-semibold text-slate-300">Quick actions</span>
                <button type="button" id="applyPreset" class="w-full rounded-xl bg-cyan-400 px-4 py-3 font-semibold text-cyan-950 hover:bg-cyan-300">Apply to all rows</button>
            </label>
        </div>

        <div class="overflow-x-auto rounded-3xl border border-slate-700/60 bg-slate-950/70">
            <table class="min-w-full divide-y divide-slate-800 text-sm">
                <thead class="bg-slate-900/90 text-slate-300">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Student</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($students as $student)
                        <tr class="attendance-row" data-user-id="{{ $student->id }}">
                            <td class="px-4 py-3 text-slate-100">
                                <div class="font-semibold">{{ $student->name }}</div>
                                <div class="text-sm text-slate-400">{{ $student->email }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <select name="attendance[{{ $loop->index }}][status]" class="row-status w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-slate-100">
                                    @foreach(['present','absent','late','excused'] as $status)
                                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="attendance[{{ $loop->index }}][user_id]" value="{{ $student->id }}">
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="attendance[{{ $loop->index }}][remarks]" class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-slate-100" placeholder="Optional remark">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-xl bg-cyan-400 px-5 py-3 font-semibold text-cyan-950 hover:bg-cyan-300">Save attendance</button>
            <a href="{{ route('admin.attendance.report') }}" class="rounded-xl border border-slate-700 bg-slate-900/60 px-5 py-3 font-semibold text-slate-100 hover:border-cyan-400/40">Open report</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('applyPreset')?.addEventListener('click', function () {
        const status = document.getElementById('bulkStatus').value;
        document.querySelectorAll('.row-status').forEach((select) => {
            select.value = status;
        });
    });
</script>
@endsection
