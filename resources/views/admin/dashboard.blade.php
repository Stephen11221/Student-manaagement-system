@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Admin Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400">System administration and control center</p>
    </div>

    <!-- Key Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total_users'] }}</p>
                </div>
                <div class="text-blue-500 text-3xl opacity-20">👥</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Active Users</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['active_users'] }}</p>
                </div>
                <div class="text-green-500 text-3xl opacity-20">✓</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Suspended Users</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['suspended_users'] }}</p>
                </div>
                <div class="text-yellow-500 text-3xl opacity-20">⏸</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Locked Users</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['locked_users'] }}</p>
                </div>
                <div class="text-red-500 text-3xl opacity-20">🔒</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Classes</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total_classes'] }}</p>
                </div>
                <div class="text-purple-500 text-3xl opacity-20">📚</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Departments</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total_departments'] }}</p>
                </div>
                <div class="text-indigo-500 text-3xl opacity-20">🏢</div>
            </div>
        </div>
    </div>

    <!-- Admin Controls Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- User Management -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center mb-4">
                <div class="text-2xl mr-3">👥</div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">User Management</h3>
            </div>
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Manage users, roles, and access control</p>
            <a href="{{ route('admin.users.index') }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg text-sm font-medium">
                Manage Users
            </a>
        </div>

        <!-- Departments -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center mb-4">
                <div class="text-2xl mr-3">🏢</div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Departments</h3>
            </div>
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Organize and manage departments</p>
            <a href="{{ route('admin.departments.index') }}" class="inline-block bg-indigo-500 hover:bg-indigo-600 text-white py-2 px-4 rounded-lg text-sm font-medium">
                Manage Departments
            </a>
        </div>

        <!-- System Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center mb-4">
                <div class="text-2xl mr-3">⚙️</div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Settings</h3>
            </div>
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Configure system settings and options</p>
            <a href="{{ route('admin.settings.index') }}" class="inline-block bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-lg text-sm font-medium">
                System Settings
            </a>
        </div>

        <!-- Permissions & Roles -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center mb-4">
                <div class="text-2xl mr-3">🔐</div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Permissions</h3>
            </div>
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Manage permissions and role assignments</p>
            <a href="{{ route('admin.permissions.index') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg text-sm font-medium">
                Manage Permissions
            </a>
        </div>

        <!-- Audit Logs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center mb-4">
                <div class="text-2xl mr-3">📋</div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Audit Logs</h3>
            </div>
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">View system activity and changes</p>
            <a href="{{ route('admin.audit-logs.index') }}" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-lg text-sm font-medium">
                View Audit Logs
            </a>
        </div>

        <!-- Access Control -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center mb-4">
                <div class="text-2xl mr-3">🛡️</div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Access Control</h3>
            </div>
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Manage user access restrictions</p>
            <a href="{{ route('admin.access-control') }}" class="inline-block bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg text-sm font-medium">
                Access Control
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Logs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Recent Activity</h3>
            <div class="space-y-3">
                @forelse($recent_logs as $log)
                    <div class="border-l-4 border-blue-500 pl-4 py-2">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            <strong>{{ $log->user?->name ?? 'System' }}</strong> - {{ strtoupper($log->action) }}
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $log->description }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="text-gray-600 dark:text-gray-400 text-sm">No recent activity</p>
                @endforelse
            </div>
        </div>

        <!-- Users by Role -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Users by Role</h3>
            <div class="space-y-3">
                @foreach($user_by_role as $role => $data)
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $role }}s</p>
                        <div class="flex items-center">
                            <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($data->count / $stats['total_users'] * 100) }}%"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-900 dark:text-white min-w-12 text-right">{{ $data->count }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
