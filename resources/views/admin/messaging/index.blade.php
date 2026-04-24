@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Messaging System</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Send messages to users and classes</p>
        </div>
        <a href="{{ route('admin.messaging.send-form') }}" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg font-medium">
            + Send Message
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-emerald-100">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-circle-check mt-0.5 text-emerald-300"></i>
                <div>
                    <div class="text-sm font-semibold text-white">Message sent</div>
                    <div class="text-sm text-emerald-100/90">{{ session('success') }}</div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-announcement-message
            tone="info"
            icon="user"
            label="One Recipient"
            title="Individual User"
            body="Send a message to a single student, trainer, coach, or staff member."
            :details="[
                ['icon' => 'paper-plane', 'label' => 'Use for', 'value' => 'Personal reminders and direct follow-up'],
                ['icon' => 'check-circle', 'label' => 'Best for', 'value' => 'One-to-one communication'],
            ]"
            cta-label="Send Message"
            cta-href="{{ route('admin.messaging.send-form') }}?type=individual"
            :show-action="false"
            :show-deadline="false"
        />

        <x-announcement-message
            tone="success"
            icon="users"
            label="Class Broadcast"
            title="Class Announcement"
            body="Send one clear message to every student in a selected class."
            :details="[
                ['icon' => 'calendar', 'label' => 'Use for', 'value' => 'Homework, class changes, and reminders'],
                ['icon' => 'check-circle', 'label' => 'Best for', 'value' => 'Group updates that need quick action'],
            ]"
            cta-label="Send Message"
            cta-href="{{ route('admin.messaging.send-form') }}?type=class"
            :show-action="false"
            :show-deadline="false"
        />

        <x-announcement-message
            tone="warning"
            icon="globe"
            label="System Broadcast"
            title="All Users"
            body="Broadcast one announcement to the whole platform except admins."
            :details="[
                ['icon' => 'bullhorn', 'label' => 'Use for', 'value' => 'School-wide alerts and general notices'],
                ['icon' => 'check-circle', 'label' => 'Best for', 'value' => 'Urgent updates that everyone must see'],
            ]"
            cta-label="Send Message"
            cta-href="{{ route('admin.messaging.send-form') }}?type=all"
            :show-action="false"
            :show-deadline="false"
        />
    </div>
</div>
@endsection
