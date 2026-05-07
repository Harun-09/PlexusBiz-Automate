<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @php
            $detectedBasePath = config('app.subfolder_path') ?: request()->getBasePath();
            $detectedBasePath = '/'.trim((string) $detectedBasePath, '/');

            if ($detectedBasePath === '/' || $detectedBasePath === '/public') {
                $detectedBasePath = '';
            } elseif (str_ends_with($detectedBasePath, '/public')) {
                $detectedBasePath = substr($detectedBasePath, 0, -7);
            }
        @endphp
        <script>
            window.__APP_BASE_PATH__ = @json($detectedBasePath);
        </script>
        @routes
        @viteReactRefresh
        @vite(['resources/js/app.jsx', "resources/js/Pages/{$page['component']}.jsx"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
