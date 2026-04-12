<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', config('app.name', 'School Portal'))</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
            };
        </script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            html, body {
                min-height: 100%;
            }

            body {
                font-family: "Instrument Sans", sans-serif;
                background:
                    radial-gradient(circle at top left, rgba(34, 211, 238, 0.12), transparent 24%),
                    radial-gradient(circle at bottom right, rgba(239, 68, 68, 0.1), transparent 22%),
                    linear-gradient(135deg, #020617 0%, #0f172a 54%, #111827 100%);
            }
        </style>
    </head>
    <body class="text-slate-100">
        @yield('content')

        @include('partials.chat-fab')
        @include('partials.chat-panel')
        @include('partials.idle-timeout-modal')

        <script src="{{ asset('js/idle-timeout.js') }}"></script>
        <script>
            document.documentElement.dataset.idleTimeout = "{{ config('idle.idle_timeout', 15) }}";
            document.documentElement.dataset.warningTime = "{{ config('idle.warning_time', 1) }}";
        </script>
    </body>
</html>
