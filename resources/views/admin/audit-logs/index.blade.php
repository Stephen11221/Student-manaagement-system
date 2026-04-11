@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Audit Logs</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">View system activity and changes</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">User</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">Action</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">Model</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">Description</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">Date</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $log->user?->name ?? 'System' }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $log->user?->email ?? 'N/A' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ strtoupper($log->action) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                            {{ $log->model_type ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ Str::limit($log->description, 50) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $log->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $log->ip_address }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">
                            No audit logs found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $logs->links() }}
    </div>
</div>
@endsection
