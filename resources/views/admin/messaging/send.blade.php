@extends('layouts.app')

@section('content')
@php
    $initialRecipientType = old('recipient_type', request('type', ''));
@endphp

<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Chat Composer</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Write a message and send it to one user, one class, or everyone.</p>
        </div>
        <a href="{{ route('admin.messaging.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800">
            <i class="fa-solid fa-arrow-left"></i> Back to Messaging
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-rose-100">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-triangle-exclamation mt-0.5 text-rose-300"></i>
                <div>
                    <div class="text-sm font-semibold text-white">Please fix the errors</div>
                    <ul class="mt-1 list-disc pl-5 text-sm text-rose-100/90">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1.05fr_0.95fr]">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-md dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-rose-100 text-rose-600 dark:bg-rose-500/15 dark:text-rose-300">
                    <i class="fa-solid fa-comments text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Write Message</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Select a recipient, write the message, and send it instantly.</p>
                </div>
            </div>

            <form action="{{ route('admin.messaging.send') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="recipient_type" class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Send To *</label>
                    <select
                        id="recipient_type"
                        name="recipient_type"
                        required
                        onchange="updateRecipientFields()"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 outline-none transition focus:border-rose-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">Select recipient type</option>
                        <option value="individual" {{ $initialRecipientType === 'individual' ? 'selected' : '' }}>Individual User</option>
                        <option value="class" {{ $initialRecipientType === 'class' ? 'selected' : '' }}>Class</option>
                        <option value="all" {{ $initialRecipientType === 'all' ? 'selected' : '' }}>All Users</option>
                    </select>
                </div>

                <div id="user-field" class="space-y-2" style="display:none;">
                    <label for="user_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Select User</label>
                    <select
                        id="user_id"
                        name="user_id"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 outline-none transition focus:border-rose-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">Choose a user</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ ucfirst(str_replace('_', ' ', $user->role)) }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Pick one person to receive the chat message.</p>
                </div>

                <div id="class-field" class="space-y-2" style="display:none;">
                    <label for="class_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Select Class</label>
                    <select
                        id="class_id"
                        name="class_id"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 outline-none transition focus:border-rose-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">Choose a class</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Every student in the selected class will receive the message.</p>
                </div>

                <div id="all-field" class="hidden rounded-2xl border border-amber-400/25 bg-amber-400/10 p-4 text-sm text-amber-50">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-exclamation-triangle mt-0.5 text-amber-300"></i>
                        <div>
                            <div class="font-semibold text-white">Broadcast Notice</div>
                            <div class="mt-1 text-amber-50/90">This message will be sent to all users in the system except admins.</div>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="subject" class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">Subject *</label>
                    <input
                        type="text"
                        id="subject"
                        name="subject"
                        value="{{ old('subject') }}"
                        required
                        placeholder="Type a subject"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-900 outline-none transition focus:border-rose-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <label for="message" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Message *</label>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Write your message like a chat reply</span>
                    </div>
                    <textarea
                        id="message"
                        name="message"
                        rows="10"
                        required
                        placeholder="Type your message here..."
                        class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-gray-900 outline-none transition focus:border-rose-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >{{ old('message') }}</textarea>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-rose-500 to-red-600 px-5 py-3 font-semibold text-white shadow-lg shadow-red-500/20 transition hover:-translate-y-0.5">
                        <i class="fa-solid fa-paper-plane"></i> Send Message
                    </button>
                    <a href="{{ route('admin.messaging.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-300 px-5 py-3 font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <x-announcement-message
                tone="warning"
                icon="bullhorn"
                label="Student Announcement"
                title="Important: Assignment Submission"
                body="Please submit your assignment before the deadline. Check the instructions carefully and make sure your file is complete and readable. Late submissions may not be accepted."
                :details="[
                    ['icon' => 'calendar', 'label' => 'Date', 'value' => 'Friday, 25 April 2026'],
                    ['icon' => 'clock', 'label' => 'Time', 'value' => '5:00 PM'],
                    ['icon' => 'location-dot', 'label' => 'Platform', 'value' => 'School Portal > Student Dashboard > Homework'],
                    ['icon' => 'check-circle', 'label' => 'Requirements', 'value' => 'PDF or DOCX file, clear file name, correct class and student name'],
                ]"
                action="Upload your assignment now and confirm that the file opens correctly before submitting."
                deadline="Deadline: 25 April 2026, 5:00 PM sharp"
            />

            <x-announcement-message
                tone="info"
                icon="chalkboard-user"
                label="Trainer Notice"
                title="Attendance Check Starts at 8:00 AM"
                body="Trainers should open the attendance sheet before class begins. Mark students immediately so the class record stays accurate."
                :details="[
                    ['icon' => 'calendar', 'label' => 'Date', 'value' => 'Monday, 28 April 2026'],
                    ['icon' => 'clock', 'label' => 'Time', 'value' => '8:00 AM'],
                    ['icon' => 'location-dot', 'label' => 'Platform', 'value' => 'Trainer Dashboard > Attendance'],
                    ['icon' => 'check-circle', 'label' => 'Requirements', 'value' => 'Internet access, class register, and student list'],
                ]"
                action="Open the attendance page before the lesson starts and submit the record immediately after class."
                deadline="Deadline: Before class starts"
                cta-label="Open Attendance"
                cta-href="{{ route('trainer.classes.index') }}"
            />

            <x-announcement-message
                tone="info"
                icon="users"
                label="Recipient Preview"
                title="Who Will Receive This?"
                body="Check the audience before you send. Each option reaches a different group, so choose carefully."
                :details="[
                    ['icon' => 'user', 'label' => 'Individual', 'value' => 'A single student, trainer, coach, or staff account'],
                    ['icon' => 'users', 'label' => 'Class', 'value' => 'All students in the selected class'],
                    ['icon' => 'globe', 'label' => 'All Users', 'value' => 'Broadcast to the whole platform except admins'],
                    ['icon' => 'check-circle', 'label' => 'Check', 'value' => 'Make sure the audience matches the message'],
                ]"
                :show-action="false"
                :show-deadline="false"
                :show-cta="false"
            />

            <x-announcement-message
                tone="success"
                icon="list-check"
                label="Message Structure"
                title="Send in Four Steps"
                body="Choose the recipient, add the subject, write the message, and send it."
                :details="[
                    ['icon' => 'circle-check', 'label' => 'Step 1', 'value' => 'Choose recipient'],
                    ['icon' => 'circle-check', 'label' => 'Step 2', 'value' => 'Add subject'],
                    ['icon' => 'circle-check', 'label' => 'Step 3', 'value' => 'Write the message'],
                    ['icon' => 'circle-check', 'label' => 'Step 4', 'value' => 'Send and confirm'],
                ]"
                :show-action="false"
                :show-deadline="false"
                :show-cta="false"
            />
        </div>
    </div>
</div>

<script>
    function updateRecipientFields() {
        const recipientType = document.getElementById('recipient_type').value;
        const userField = document.getElementById('user-field');
        const classField = document.getElementById('class-field');
        const allField = document.getElementById('all-field');

        userField.style.display = 'none';
        classField.style.display = 'none';
        allField.style.display = 'none';

        if (recipientType === 'individual') {
            userField.style.display = 'block';
        } else if (recipientType === 'class') {
            classField.style.display = 'block';
        } else if (recipientType === 'all') {
            allField.style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', updateRecipientFields);
</script>
@endsection
