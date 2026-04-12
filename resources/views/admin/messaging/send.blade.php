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
        <div class="mb-6 rounded-lg border border-red-400 bg-red-100 px-4 py-3 text-red-700">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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

                <div id="all-field" class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 dark:border-rose-800 dark:bg-rose-500/10 dark:text-rose-200" style="display:none;">
                    <strong>Notice:</strong> This message will be sent to all users in the system except admins.
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
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-md dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-bold text-gray-900 dark:text-white">Recipient Preview</h3>
                <div class="space-y-3 text-sm text-gray-700 dark:text-gray-300">
                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900">
                        <div class="mb-1 font-semibold text-gray-900 dark:text-white">Individual User</div>
                        <div>Pick a single student, trainer, coach, or staff account.</div>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900">
                        <div class="mb-1 font-semibold text-gray-900 dark:text-white">Class</div>
                        <div>Send one message to every student in the selected class.</div>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900">
                        <div class="mb-1 font-semibold text-gray-900 dark:text-white">All Users</div>
                        <div>Broadcast an announcement to the whole platform except admins.</div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-md dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-bold text-gray-900 dark:text-white">Message Structure</h3>
                <div class="space-y-3 text-sm text-gray-700 dark:text-gray-300">
                    <p><span class="font-semibold text-rose-600 dark:text-rose-400">1. Choose recipient:</span> user, class, or all users.</p>
                    <p><span class="font-semibold text-rose-600 dark:text-rose-400">2. Add subject:</span> this becomes the notification title.</p>
                    <p><span class="font-semibold text-rose-600 dark:text-rose-400">3. Write message:</span> this becomes the notification body.</p>
                    <p><span class="font-semibold text-rose-600 dark:text-rose-400">4. Send:</span> the system creates notifications for each recipient.</p>
                </div>
            </div>
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
