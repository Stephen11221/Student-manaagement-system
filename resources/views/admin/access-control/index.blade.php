@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Access Control</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Manage user access and restrictions</p>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <p class="text-gray-600 dark:text-gray-400 text-sm">Inactive Users</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['inactive'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-orange-500">
            <p class="text-gray-600 dark:text-gray-400 text-sm">Suspended Users</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['suspended'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-red-500">
            <p class="text-gray-600 dark:text-gray-400 text-sm">Locked Users</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['locked'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <p class="text-gray-600 dark:text-gray-400 text-sm">Total Restricted</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_restricted'] }}</p>
        </div>
    </div>

    <!-- Restricted Users Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">User</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">Email</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">Department</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium 
                                {{ $user->status === 'suspended' ? 'bg-orange-100 text-orange-800' : '' }}
                                {{ $user->status === 'locked' ? 'bg-red-100 text-red-800' : '' }}
                                {{ !$user->is_active ? 'bg-yellow-100 text-yellow-800' : '' }}
                            ">
                                {{ ucfirst($user->status) }} {{ !$user->is_active ? '(Inactive)' : '' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $user->department?->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            @if(!$user->isActive())
                                <form action="{{ route('admin.users.activate.new', $user) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="text-green-500 hover:text-green-700 mr-3">Activate</button>
                                </form>
                            @endif
                            
                            @if(!$user->isSuspended() && $user->status !== 'locked')
                                <form action="{{ route('admin.users.suspend.new', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Suspend this user?')">
                                    @csrf
                                    <button type="submit" class="text-yellow-500 hover:text-yellow-700 mr-3">Suspend</button>
                                </form>
                            @endif

                            @if(!$user->isLocked())
                                <form action="{{ route('admin.users.lock', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Lock this account?')">
                                    @csrf
                                    <button type="submit" class="text-red-500 hover:text-red-700">Lock</button>
                                </form>
                            @else
                                <form action="{{ route('admin.users.unlock', $user) }}" method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit" class="text-blue-500 hover:text-blue-700">Unlock</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">
                            No restricted users found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection
