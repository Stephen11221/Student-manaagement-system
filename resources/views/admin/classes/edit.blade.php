@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Edit Class</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Update class information</p>
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
        <form action="{{ route('admin.classes.update', $class) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Class Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $class->name) }}" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                <textarea id="description" name="description" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ old('description', $class->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="trainer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Trainer *</label>
                    <select id="trainer_id" name="trainer_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        @foreach ($trainers as $trainer)
                            <option value="{{ $trainer->id }}" {{ old('trainer_id', $class->trainer_id) == $trainer->id ? 'selected' : '' }}>
                                {{ $trainer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="max_students" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Students</label>
                    <input type="number" id="max_students" name="max_students" value="{{ old('max_students', $class->max_students) }}" min="1"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="room_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Room Number</label>
                    <input type="text" id="room_number" name="room_number" value="{{ old('room_number', $class->room_number) }}"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>

                <div>
                    <label for="delivery_mode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Class Mode *</label>
                    <select id="delivery_mode" name="delivery_mode" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="online" {{ old('delivery_mode', $class->delivery_mode) == 'online' ? 'selected' : '' }}>Online</option>
                        <option value="physical" {{ old('delivery_mode', $class->delivery_mode ?? 'physical') == 'physical' ? 'selected' : '' }}>Physical</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                    <select id="status" name="status" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="active" {{ old('status', $class->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $class->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded-lg font-medium">
                    Update Class
                </button>
                <a href="{{ route('admin.classes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-6 rounded-lg font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
