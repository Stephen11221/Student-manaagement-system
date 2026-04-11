@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Departments</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Manage organizational departments</p>
        </div>
        <a href="{{ route('admin.departments.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg font-medium">
            + New Department
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">Department</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">Head</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">Users</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 dark:text-white">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($departments as $dept)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $dept->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $dept->description }}</p>
                        </td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $dept->head?->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $dept->active_users_count ?? 0 }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $dept->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $dept->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.departments.edit', $dept) }}" class="text-blue-500 hover:text-blue-700 mr-3">Edit</a>
                            <form action="{{ route('admin.departments.delete', $dept) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">
                            No departments found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $departments->links() }}
    </div>
</div>
@endsection
