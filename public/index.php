<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (PHP_SAPI !== 'cli' && isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI'])) {
    $requestUri = $_SERVER['REQUEST_URI'];
    $uriParts = parse_url($requestUri);

    if (is_array($uriParts)) {
        $path = $uriParts['path'] ?? null;
        $path = is_string($path) ? $path : '';

        if ($path !== '') {
            $hasTrailingSlash = str_ends_with($path, '/');
            $segments = array_values(array_filter(
                explode('/', trim($path, '/')),
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

            $canonicalPath = '/'.implode('/', $segments);

            if ($canonicalPath !== '/' && $hasTrailingSlash) {
                $canonicalPath .= '/';
            }

            if ($canonicalPath !== $path) {
                $query = $uriParts['query'] ?? null;
                $query = is_string($query) && $query !== '' ? '?'.$query : '';

                header('Location: '.$canonicalPath.$query, true, 302);
                exit;
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
