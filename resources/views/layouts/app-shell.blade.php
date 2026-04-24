<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', config('app.name', 'School Portal'))</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            select,
            select option {
                background-color: #0f172a !important;
                color: #f8fafc !important;
            }

            select {
                color-scheme: dark;
            }
        </style>
        @stack('styles')
    </head>
    <body class="dark" data-sidebar-collapsed="false" data-sidebar-open="false">
        <div class="app-shell">
            @include('partials.role-sidebar', ['role' => $sidebarRole ?? 'student', 'classId' => $sidebarClassId ?? null])

            <main class="app-main">
                @yield('content')
            </main>
        </div>

        @unless($hideChat ?? false)
            @include('partials.chat-fab')
            @include('partials.chat-panel')
        @endunless
        @include('partials.idle-timeout-modal')

        <script src="{{ asset('js/idle-timeout.js') }}"></script>
        <script>
            document.documentElement.dataset.idleTimeout = "{{ config('idle.idle_timeout', 15) }}";
            document.documentElement.dataset.warningTime = "{{ config('idle.warning_time', 1) }}";
        </script>
    </body>
</html>
