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
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Send to Individual User -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center mb-4">
                <div class="text-2xl mr-3">👤</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Individual User</h3>
            </div>
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Send message to a single user</p>
            <a href="{{ route('admin.messaging.send-form') }}?type=individual" class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg text-sm font-medium">
                Send Message
            </a>
        </div>

        <!-- Send to Class -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center mb-4">
                <div class="text-2xl mr-3">👥</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Class</h3>
            </div>
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Send message to all students in a class</p>
            <a href="{{ route('admin.messaging.send-form') }}?type=class" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg text-sm font-medium">
                Send Message
            </a>
        </div>

        <!-- Send to All -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center mb-4">
                <div class="text-2xl mr-3">🌍</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">All Users</h3>
            </div>
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Send message to all users in the system</p>
            <a href="{{ route('admin.messaging.send-form') }}?type=all" class="inline-block bg-purple-500 hover:bg-purple-600 text-white py-2 px-4 rounded-lg text-sm font-medium">
                Send Message
            </a>
        </div>
    </div>
</div>
@endsection
