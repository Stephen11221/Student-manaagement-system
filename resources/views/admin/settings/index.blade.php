@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">System Settings</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Configure application settings and options</p>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf

            @foreach($allSettings as $group => $groupSettings)
                <div class="mb-8 pb-8 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 capitalize">{{ $group }} Settings</h3>

                    <div class="space-y-4">
                        @foreach($groupSettings as $setting)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 capitalize">
                                    {{ str_replace('_', ' ', $setting->key) }}
                                </label>

                                @if($setting->type === 'boolean')
                                    <input type="checkbox" name="{{ $setting->key }}" value="1" 
                                        {{ $setting->value == '1' ? 'checked' : '' }}
                                        class="h-4 w-4 text-blue-500 border-gray-300 rounded">
                                @elseif($setting->type === 'json')
                                    <textarea name="{{ $setting->key }}" rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ is_string($setting->value) ? $setting->value : json_encode($setting->value) }}</textarea>
                                @else
                                    <input type="{{ $setting->type === 'integer' ? 'number' : 'text' }}" name="{{ $setting->key }}" value="{{ $setting->value }}"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                @endif

                                @if($setting->description)
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $setting->description }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="mt-6">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded-lg font-medium">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
