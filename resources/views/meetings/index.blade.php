@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $upcomingMeetings = $meetings->getCollection()->filter(fn ($meeting) => $meeting->scheduled_at?->isFuture());
    $pastMeetings = $meetings->getCollection()->filter(fn ($meeting) => $meeting->scheduled_at?->isPast());
@endphp

<div class="container mx-auto px-4 py-8 text-slate-100">
    <div class="mb-8 rounded-3xl border border-slate-700/60 bg-slate-950/35 px-5 py-5 shadow-2xl backdrop-blur-sm lg:px-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="mb-3 inline-flex rounded-full border border-cyan-400/30 bg-cyan-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-cyan-200">
                    Shared meetings board
                </p>
                <h1 class="text-4xl font-bold text-slate-50">Meetings</h1>
                <p class="mt-2 max-w-2xl text-slate-300">
                    View team meetings across the portal. Team sessions are visible to everyone, while your own individual meetings remain visible to you and admins.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-sm font-semibold text-slate-200 hover:border-cyan-400/40">
                    <i class="fa-solid fa-house"></i> Dashboard
                </a>
                <a href="{{ route('notifications.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-4 py-3 text-sm font-semibold text-cyan-950 hover:bg-cyan-400">
                    <i class="fa-regular fa-bell"></i> Notifications
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4 mb-8">
        <div class="rounded-2xl border border-slate-700/60 bg-slate-950/60 p-5">
            <div class="text-sm text-slate-400">Visible meetings</div>
            <div class="mt-2 text-3xl font-bold text-slate-50">{{ $meetings->total() }}</div>
        </div>
        <div class="rounded-2xl border border-slate-700/60 bg-slate-950/60 p-5">
            <div class="text-sm text-slate-400">Upcoming</div>
            <div class="mt-2 text-3xl font-bold text-slate-50">{{ $upcomingCount ?? $upcomingMeetings->count() }}</div>
        </div>
        <div class="rounded-2xl border border-slate-700/60 bg-slate-950/60 p-5">
            <div class="text-sm text-slate-400">Past</div>
            <div class="mt-2 text-3xl font-bold text-slate-50">{{ $pastCount ?? $pastMeetings->count() }}</div>
        </div>
        <div class="rounded-2xl border border-slate-700/60 bg-slate-950/60 p-5">
            <div class="text-sm text-slate-400">Your role</div>
            <div class="mt-2 text-3xl font-bold text-slate-50">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</div>
        </div>
    </div>

    <div class="space-y-8">
        <section class="rounded-3xl border border-slate-700/60 bg-slate-950/45 p-6 shadow-lg">
            <div class="mb-5 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-bold text-slate-50">Upcoming Meetings</h2>
                    <p class="text-sm text-slate-400">Upcoming sessions that are visible to you.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                @forelse ($upcomingMeetings as $meeting)
                    <article class="rounded-2xl border border-slate-700/70 bg-slate-900/70 p-5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-lg font-semibold text-slate-50">{{ $meeting->title }}</h3>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $meeting->meeting_type === 'online' ? 'bg-sky-500/15 text-sky-300' : 'bg-amber-500/15 text-amber-200' }}">
                                        {{ ucfirst($meeting->meeting_type) }}
                                    </span>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $meeting->audience_type === 'team' ? 'bg-cyan-500/15 text-cyan-300' : 'bg-violet-500/15 text-violet-300' }}">
                                        {{ ucfirst($meeting->audience_type) }}
                                    </span>
                                </div>
                                <p class="mt-2 text-sm text-slate-300">
                                    {{ $meeting->scheduled_at?->format('M d, Y g:i A') ?? '-' }}
                                    @if ($meeting->audience_type === 'individual' && $meeting->staff && $meeting->staff->id === $user->id)
                                        · For you
                                    @elseif ($meeting->audience_type === 'individual' && $meeting->staff)
                                        · {{ $meeting->staff->name }}
                                    @endif
                                </p>
                                <p class="mt-1 text-sm text-slate-400">
                                    {{ $meeting->team_role ? ucfirst(str_replace('_', ' ', $meeting->team_role)) : 'General' }}
                                </p>
                            </div>
                            <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-200">
                                {{ ucfirst($meeting->status) }}
                            </span>
                        </div>

                        <div class="mt-4 grid gap-3 text-sm text-slate-300">
                            <div class="rounded-xl border border-slate-700/60 bg-slate-950/60 p-3">
                                <span class="block text-xs uppercase tracking-wider text-slate-500">Join / Location</span>
                                <div class="mt-2 flex flex-wrap items-center gap-3">
                                    <span class="block">
                                        {{ $meeting->meeting_type === 'online' ? ($meeting->meeting_link ?? 'No link added') : ($meeting->location ?? 'No location added') }}
                                    </span>
                                    @if ($meeting->meeting_type === 'online' && $meeting->meeting_link)
                                        <a href="{{ $meeting->meeting_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-3 py-2 text-xs font-semibold text-cyan-950 hover:bg-cyan-400">
                                            <i class="fa-solid fa-video"></i> Join
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @if ($meeting->notes)
                                <div class="rounded-xl border border-slate-700/60 bg-slate-950/60 p-3">
                                    <span class="block text-xs uppercase tracking-wider text-slate-500">Notes</span>
                                    <span class="mt-1 block">{{ $meeting->notes }}</span>
                                </div>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-700/70 bg-slate-900/40 p-6 text-sm text-slate-400">
                        No upcoming meetings visible to you yet.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="rounded-3xl border border-slate-700/60 bg-slate-950/45 p-6 shadow-lg">
            <div class="mb-5">
                <h2 class="text-2xl font-bold text-slate-50">Recent Meetings</h2>
                <p class="text-sm text-slate-400">A history of recent sessions visible to you.</p>
            </div>

            <div class="space-y-4">
                @forelse ($pastMeetings as $meeting)
                    <article class="rounded-2xl border border-slate-700/70 bg-slate-900/70 p-5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-lg font-semibold text-slate-50">{{ $meeting->title }}</h3>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $meeting->meeting_type === 'online' ? 'bg-sky-500/15 text-sky-300' : 'bg-amber-500/15 text-amber-200' }}">
                                        {{ ucfirst($meeting->meeting_type) }}
                                    </span>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold bg-slate-700/60 text-slate-200">
                                        {{ ucfirst($meeting->audience_type) }}
                                    </span>
                                </div>
                                <p class="mt-2 text-sm text-slate-300">{{ $meeting->scheduled_at?->format('M d, Y g:i A') ?? '-' }}</p>
                            </div>
                            <span class="rounded-full bg-slate-700/60 px-3 py-1 text-xs font-semibold text-slate-200">
                                {{ ucfirst($meeting->status) }}
                            </span>
                        </div>
                        @if ($meeting->meeting_type === 'online' && $meeting->meeting_link)
                            <div class="mt-4">
                                <a href="{{ $meeting->meeting_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-4 py-2 text-sm font-semibold text-cyan-950 hover:bg-cyan-400">
                                    <i class="fa-solid fa-video"></i> Join Meeting
                                </a>
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-700/70 bg-slate-900/40 p-6 text-sm text-slate-400">
                        No past meetings visible to you yet.
                    </div>
                @endforelse
            </div>
        </section>

        <div class="mt-2">{{ $meetings->links() }}</div>
    </div>
</div>
@endsection
