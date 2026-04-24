@extends('layouts.app')

@section('content')
@php
    $roleTabs = [
        'trainer' => route('admin.staff.index', 'trainer'),
        'accountant' => route('admin.staff.index', 'accountant'),
        'career_coach' => route('admin.staff.index', 'career_coach'),
    ];
@endphp

<div class="container mx-auto px-4 py-8 text-slate-100">
    <div class="mb-8 rounded-3xl border border-slate-700/60 bg-slate-950/35 px-5 py-5 shadow-2xl backdrop-blur-sm lg:px-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="mb-3 flex flex-wrap gap-2">
                @foreach ($roleTabs as $key => $url)
                    <a href="{{ $url }}" class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold {{ $role === $key ? 'border-cyan-400 bg-cyan-400/15 text-cyan-200' : 'border-slate-700 bg-slate-900/70 text-slate-300 hover:border-cyan-400/40 hover:text-white' }}">
                        {{ ucfirst(str_replace('_', ' ', $key)) }}
                    </a>
                @endforeach
            </div>
            <h1 class="text-4xl font-bold text-slate-50">Schedule {{ $roleLabel }} Meeting</h1>
            <p class="mt-2 text-slate-300">Create a team meeting or invite one person from the team.</p>
        </div>
        <a href="{{ route('admin.staff.index', $role) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-3 text-sm font-semibold text-slate-200 hover:border-cyan-400/40">
            <i class="fa-solid fa-arrow-left"></i> Back to {{ $roleLabel }}
        </a>
    </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-rose-100">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.05fr_0.95fr]">
            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                <form method="POST" action="{{ route('admin.staff.meetings.store', $role) }}" class="space-y-5" id="meeting-form">
                @csrf

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Meeting Scope *</label>
                        <select name="audience_type" id="audience_type" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                            <option value="team" @selected(old('audience_type', 'team') === 'team')>Team Meeting</option>
                            <option value="individual" @selected(old('audience_type') === 'individual')>Individual Meeting</option>
                        </select>
                    </div>

                    <div id="staff-field" class="{{ old('audience_type') === 'individual' ? '' : 'hidden' }}">
                        <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Select Staff Member *</label>
                        <select name="staff_id" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Choose a staff member</option>
                            @foreach ($staff as $member)
                                <option value="{{ $member->id }}" @selected((string) old('staff_id') === (string) $member->id)>{{ $member->name }} ({{ $member->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Meeting Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Weekly coordination meeting" required>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Meeting Type *</label>
                        <select name="meeting_type" id="meeting_type" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                            <option value="online" @selected(old('meeting_type', 'online') === 'online')>Online</option>
                            <option value="physical" @selected(old('meeting_type') === 'physical')>Physical</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Scheduled For *</label>
                        <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
                    </div>
                </div>

                <div id="meeting-link-field" class="{{ old('meeting_type', 'online') === 'online' ? '' : 'hidden' }}">
                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Meeting Link *</label>
                    <input type="url" name="meeting_link" value="{{ old('meeting_link') }}" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="https://zoom.us/j/...">
                </div>

                <div id="location-field" class="{{ old('meeting_type') === 'physical' ? '' : 'hidden' }}">
                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Physical Location *</label>
                    <input type="text" name="location" value="{{ old('location') }}" class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Admin boardroom or office">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Notes</label>
                    <textarea name="notes" rows="5" class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Agenda, talking points, reminders...">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-5 py-3 font-semibold text-cyan-950 hover:bg-cyan-400">
                    <i class="fa-solid fa-calendar-check"></i> Save Meeting
                </button>
            </form>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Meeting Format</h2>
                <div class="mt-4 space-y-4 text-sm text-gray-600 dark:text-gray-300">
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                        <div class="font-semibold text-gray-900 dark:text-white">Team meeting</div>
                        <p class="mt-1">Use this when the whole {{ strtolower($roleLabel) }} group should attend the same session.</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                        <div class="font-semibold text-gray-900 dark:text-white">Individual meeting</div>
                        <p class="mt-1">Use this when the session is only for one staff member.</p>
                    </div>
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/40">
                        <div class="font-semibold text-gray-900 dark:text-white">Online or physical</div>
                        <p class="mt-1">Online meetings need a link. Physical meetings need a room or venue.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const audienceType = document.getElementById('audience_type');
    const meetingType = document.getElementById('meeting_type');
    const staffField = document.getElementById('staff-field');
    const linkField = document.getElementById('meeting-link-field');
    const locationField = document.getElementById('location-field');

    function syncMeetingFields() {
        const isIndividual = audienceType.value === 'individual';
        const isPhysical = meetingType.value === 'physical';

        staffField.classList.toggle('hidden', !isIndividual);
        linkField.classList.toggle('hidden', isPhysical);
        locationField.classList.toggle('hidden', !isPhysical);
    }

    audienceType.addEventListener('change', syncMeetingFields);
    meetingType.addEventListener('change', syncMeetingFields);
    syncMeetingFields();
</script>
@endsection
