<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/project-logo.png') }}?v=1">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @php
            $detectedBasePath = (string) (parse_url(url('/'), PHP_URL_PATH) ?? '');
            $detectedBasePath = '/'.trim($detectedBasePath, '/');

            if ($detectedBasePath === '/') {
                $detectedBasePath = '';
            } else {
                $hasTrailingSlash = str_ends_with($detectedBasePath, '/');
                $segments = array_values(array_filter(
                    explode('/', trim($detectedBasePath, '/')),
                    static fn (string $segment): bool => $segment !== ''
                ));

                if (count($segments) >= 4) {
                    do {
                        $updated = false;
                        $segmentCount = count($segments);

                        for ($len = intdiv($segmentCount, 2); $len >= 2; $len--) {
                            $head = array_slice($segments, 0, $len);
                            $next = array_slice($segments, $len, $len);

                            if ($head === $next && in_array('public', $head, true)) {
                                $segments = array_merge($head, array_slice($segments, $len * 2));
                                $updated = true;
                                break;
                            }
                        }
                    } while ($updated);
                }

                $detectedBasePath = '/'.implode('/', $segments);

                if ($detectedBasePath !== '/' && $hasTrailingSlash) {
                    $detectedBasePath .= '/';
                }
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
