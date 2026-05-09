<?php

declare(strict_types=1);

// One-time emergency cache clear endpoint for shared hosting (no terminal access).
// Usage:
// 1) Upload this file to public/.
// 2) Open: /plexusbiz/public/cache-clear-once.php?key=REPLACE_WITH_STRONG_KEY
// 3) Confirm success output.
// 4) Delete this file immediately (or rely on auto-delete attempt below).

$placeholderKey = 'REPLACE_WITH_STRONG_KEY';
$expectedKey = 'Plexus_2026_Clear_X9!';
$inputKey = isset($_GET['key']) ? (string) $_GET['key'] : '';

if ($expectedKey === $placeholderKey) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Set a strong key in \$expectedKey first.\n";
    exit;
}

if (! hash_equals($expectedKey, $inputKey)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo "Forbidden.\n";
    exit;
}

header('Content-Type: text/plain; charset=UTF-8');

$basePath = dirname(__DIR__);

if (! is_file($basePath.'/artisan')) {
    http_response_code(500);
    echo "Invalid project path. artisan not found.\n";
    exit;
}

chdir($basePath);

require $basePath.'/vendor/autoload.php';
$app = require_once $basePath.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$commands = [
    'route:clear',
    'config:clear',
    'view:clear',
    'cache:clear',
    'event:clear',
    'optimize:clear',
];

foreach ($commands as $command) {
    try {
        $exitCode = $kernel->call($command);
        echo '['.$command."] exit=".$exitCode."\n";
        $output = trim($kernel->output());
        if ($output !== '') {
            echo $output."\n";
        }
    } catch (Throwable $e) {
        echo '['.$command."] failed: ".$e->getMessage()."\n";
    }
}

// Fallback: force-remove cached bootstrap files if command-level clear misses anything.
$bootstrapCachePath = $basePath.'/bootstrap/cache';
$cacheGlobs = [
    $bootstrapCachePath.'/routes-*.php',
    $bootstrapCachePath.'/config.php',
    $bootstrapCachePath.'/events.php',
];

foreach ($cacheGlobs as $pattern) {
    foreach (glob($pattern) ?: [] as $cacheFile) {
        if (@unlink($cacheFile)) {
            echo '[delete] '.$cacheFile." removed\n";
        } else {
            echo '[delete] '.$cacheFile." could not be removed\n";
        }
    }
}

$kernel->terminate(
    Illuminate\Http\Request::capture(),
    new Symfony\Component\HttpFoundation\Response('')
);

if (function_exists('opcache_reset')) {
    @opcache_reset();
    echo "opcache_reset: attempted\n";
}

if (function_exists('apcu_clear_cache')) {
    @apcu_clear_cache();
    echo "apcu_clear_cache: attempted\n";
}

clearstatcache();

echo "Done.\n";

// Auto-delete best-effort. If this fails, delete manually from hosting panel.
if (@unlink(__FILE__)) {
    echo "Self-delete: success\n";
} else {
    echo "Self-delete: failed (delete manually now)\n";
}
