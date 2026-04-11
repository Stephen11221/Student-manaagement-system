@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Roles & Permissions</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Manage role-based permissions</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">Role</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">Description</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">Permissions</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($roles as $role)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 dark:text-white capitalize">{{ $role->name }}</p>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                            {{ $role->description ?? 'No description' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($role->permissions as $perm)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded text-xs">
                                        {{ $perm->name }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.roles.edit', $role) }}" class="text-blue-500 hover:text-blue-700">Manage Permissions</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">
                            No roles found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $roles->links() }}
    </div>
</div>
@endsection
