<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#171717">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <script>
            (function () {
                var stored = localStorage.getItem('theme');
                var theme = stored === 'light' || stored === 'dark' ? stored : 'dark';
                var dark = theme === 'dark';
                var root = document.documentElement;
                root.classList.toggle('dark', dark);
                root.dataset.theme = theme;
                root.style.colorScheme = dark ? 'dark' : 'light';
            })();
        </script>

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
