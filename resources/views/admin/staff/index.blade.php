@extends('layouts.app')

@section('content')
@php
    $roleTabs = [
        'trainer' => ['label' => 'Trainers', 'icon' => 'chalkboard-user', 'route' => route('admin.staff.index', 'trainer')],
        'accountant' => ['label' => 'Accountants', 'icon' => 'calculator', 'route' => route('admin.staff.index', 'accountant')],
        'career_coach' => ['label' => 'Career Coaches', 'icon' => 'briefcase', 'route' => route('admin.staff.index', 'career_coach')],
    ];
@endphp

<div class="container mx-auto px-4 py-8 text-slate-100">
    <div class="mb-8 rounded-3xl border border-slate-700/60 bg-slate-950/35 px-5 py-5 shadow-2xl backdrop-blur-sm lg:px-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="mb-3 flex flex-wrap gap-2">
                @foreach ($roleTabs as $key => $tab)
                    <a href="{{ $tab['route'] }}" class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold {{ $role === $key ? 'border-cyan-400 bg-cyan-400/15 text-cyan-200' : 'border-slate-700 bg-slate-900/70 text-slate-300 hover:border-cyan-400/40 hover:text-white' }}">
                        <i class="fa-solid fa-{{ $tab['icon'] }}"></i> {{ $tab['label'] }}
                    </a>
                @endforeach
            </div>
            <h1 class="text-4xl font-bold text-slate-50">{{ $roleLabel }} Management</h1>
            <p class="mt-2 text-slate-300">Manage {{ strtolower($roleLabel) }}, review status, and schedule team or individual meetings.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.staff.meetings.create', $role) }}" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-4 py-3 text-sm font-semibold text-cyan-950 hover:bg-cyan-400">
                <i class="fa-solid fa-calendar-plus"></i> Schedule Meeting
            </a>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-sm font-semibold text-slate-200 hover:border-cyan-400/40">
                <i class="fa-solid fa-users"></i> All Users
            </a>
        </div>
    </div>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-emerald-100">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4 mb-8">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="text-sm text-gray-500 dark:text-gray-400">Total {{ $roleLabel }}</div>
            <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_staff'] }}</div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="text-sm text-gray-500 dark:text-gray-400">Active</div>
            <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['active_staff'] }}</div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="text-sm text-gray-500 dark:text-gray-400">Team Meetings</div>
            <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['team_meetings'] }}</div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="text-sm text-gray-500 dark:text-gray-400">Individual Meetings</div>
            <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['individual_meetings'] }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1fr_1.15fr]">
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-bold text-slate-50">{{ $roleLabel }}</h2>
                    <p class="text-sm text-slate-400">People in this team</p>
                </div>
                <span class="inline-flex items-center gap-2 rounded-full bg-cyan-500/10 px-3 py-1 text-xs font-semibold text-cyan-300">
                    <i class="fa-solid fa-{{ $roleIcon }}"></i> Team View
                </span>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($staff as $member)
                            <tr class="bg-white dark:bg-gray-800">
                                <td class="px-4 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ $member->name }}</td>
                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $member->email }}</td>
                                <td class="px-4 py-4 text-sm">
                                    @if ($member->deleted_at)
                                        <span class="rounded-full bg-rose-500/15 px-3 py-1 text-xs font-semibold text-rose-200">Suspended</span>
                                    @elseif (! $member->is_active)
                                        <span class="rounded-full bg-amber-500/15 px-3 py-1 text-xs font-semibold text-amber-200">Inactive</span>
                                    @else
                                        <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-200">Active</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm">
                                    <a href="{{ route('admin.users.edit', $member->id) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-700 px-3 py-2 text-xs font-semibold text-gray-200 hover:border-cyan-400/40">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No {{ strtolower($roleLabel) }} found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $staff->links() }}</div>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Scheduled Meetings</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Upcoming and past meetings for this team</p>
                    </div>
                    <a href="{{ route('admin.staff.meetings.create', $role) }}" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-4 py-2 text-sm font-semibold text-cyan-950 hover:bg-cyan-400">
                        <i class="fa-solid fa-plus"></i> New
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse ($meetings as $meeting)
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $meeting->title }}</h3>
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $meeting->meeting_type === 'online' ? 'bg-sky-500/15 text-sky-300' : 'bg-amber-500/15 text-amber-200' }}">
                                            {{ ucfirst($meeting->meeting_type) }}
                                        </span>
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $meeting->audience_type === 'team' ? 'bg-cyan-500/15 text-cyan-300' : 'bg-violet-500/15 text-violet-300' }}">
                                            {{ ucfirst($meeting->audience_type) }}
                                        </span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $meeting->scheduled_at?->format('M d, Y g:i A') ?? '-' }}
                                        @if ($meeting->audience_type === 'individual' && $meeting->staff)
                                            · {{ $meeting->staff->name }}
                                        @endif
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $meeting->meeting_type === 'online' ? ($meeting->meeting_link ?? 'No link added') : ($meeting->location ?? 'No location added') }}
                                    </p>
                                </div>
                                <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-200">
                                    {{ ucfirst($meeting->status) }}
                                </span>
                            </div>
                            @if ($meeting->notes)
                                <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">{{ $meeting->notes }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-300 p-6 text-center text-sm text-slate-500 dark:border-gray-700 dark:text-gray-400">
                            No meetings scheduled for this team yet.
                        </div>
                    @endforelse
                </div>

                <div class="mt-4">{{ $meetings->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
