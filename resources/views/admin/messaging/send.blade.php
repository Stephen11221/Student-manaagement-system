@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Send Message</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Create and send messages to users</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <form action="{{ route('admin.messaging.send') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="recipient_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Send To *</label>
                <select id="recipient_type" name="recipient_type" required onchange="updateRecipientFields()"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Select recipient type</option>
                    <option value="individual" {{ old('recipient_type') == 'individual' ? 'selected' : '' }}>Individual User</option>
                    <option value="class" {{ old('recipient_type') == 'class' ? 'selected' : '' }}>Class</option>
                    <option value="all" {{ old('recipient_type') == 'all' ? 'selected' : '' }}>All Users</option>
                </select>
            </div>

            <!-- Individual User Field -->
            <div id="user-field" class="mb-4" style="display: none;">
                <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select User</label>
                <select id="user_id" name="user_id"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Choose a user</option>
                    @if(isset($users))
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ ucfirst($user->role) }})
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Class Field -->
            <div id="class-field" class="mb-4" style="display: none;">
                <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Class</label>
                <select id="class_id" name="class_id"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Choose a class</option>
                    @if(isset($classes))
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- All Users Notice -->
            <div id="all-field" class="mb-4 p-4 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg" style="display: none;">
                <p class="text-blue-800 dark:text-blue-200 text-sm">
                    <strong>⚠️ Notice:</strong> This message will be sent to all users in the system (except admins).
                </p>
            </div>

            <div class="mb-4">
                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject *</label>
                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    placeholder="Enter message subject">
            </div>

            <div class="mb-6">
                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message *</label>
                <textarea id="message" name="message" rows="8" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                    placeholder="Enter your message here...">{{ old('message') }}</textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded-lg font-medium">
                    Send Message
                </button>
                <a href="{{ route('admin.messaging.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-6 rounded-lg font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function updateRecipientFields() {
        const recipientType = document.getElementById('recipient_type').value;
        const userField = document.getElementById('user-field');
        const classField = document.getElementById('class-field');
        const allField = document.getElementById('all-field');

        // Hide all fields
        userField.style.display = 'none';
        classField.style.display = 'none';
        allField.style.display = 'none';

        // Show selected field
        if (recipientType === 'individual') {
            userField.style.display = 'block';
        } else if (recipientType === 'class') {
            classField.style.display = 'block';
        } else if (recipientType === 'all') {
            allField.style.display = 'block';
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', updateRecipientFields);
</script>
@endsection
