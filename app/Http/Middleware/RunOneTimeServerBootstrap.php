<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RunOneTimeServerBootstrap
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->settingEnabled() || app()->runningInConsole()) {
            return $next($request);
        }

        $this->clearSubdirectoryRouteCacheIfNeeded();

        $markerPath = $this->markerPath();
        $lockPath = $markerPath.'.lock';

        if (is_file($markerPath)) {
            return $next($request);
        }

        $this->ensureDirectory(dirname($markerPath));

        $lockHandle = @fopen($lockPath, 'c+');

        if ($lockHandle === false) {
            Log::warning('One-time bootstrap lock file could not be opened.', ['path' => $lockPath]);

            return $next($request);
        }

        if (! flock($lockHandle, LOCK_EX)) {
            fclose($lockHandle);

            return $next($request);
        }

        try {
            clearstatcache(true, $markerPath);
            if (! is_file($markerPath)) {
                $result = $this->runBootstrapCommands();

                if ($result['success']) {
                    file_put_contents(
                        $markerPath,
                        json_encode([
                            'attempted_at' => now()->toIso8601String(),
                            'app_env' => app()->environment(),
                            'success' => true,
                            'commands' => $result['commands'],
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                    );
                } else {
                    file_put_contents(
                        $markerPath.'.failed',
                        json_encode([
                            'attempted_at' => now()->toIso8601String(),
                            'app_env' => app()->environment(),
                            'success' => false,
                            'commands' => $result['commands'],
                            'error' => $result['error'],
                        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                    );
                }

                if (! $result['success'] && $this->settingAbortOnFailure()) {
                    abort(500, 'One-time server bootstrap failed. Check logs and storage marker file.');
                }
            }
        } finally {
            flock($lockHandle, LOCK_UN);
            fclose($lockHandle);
        }

        return $next($request);
    }

    private function runBootstrapCommands(): array
    {
        $commands = [
            [
                'name' => 'storage:link',
                'arguments' => $this->settingStorageLinkForce() ? ['--force' => true] : [],
            ],
            ['name' => 'route:clear', 'arguments' => []],
            ['name' => 'optimize:clear', 'arguments' => []],
            ['name' => 'migrate', 'arguments' => ['--force' => true]],
        ];

        if ($this->settingSeedEnabled()) {
            $commands[] = ['name' => 'db:seed', 'arguments' => ['--force' => true]];
        }

        $commands[] = ['name' => 'queue:restart', 'arguments' => []];
        $commands[] = ['name' => 'schedule:run', 'arguments' => []];

        $executed = [];
        $error = null;

        foreach ($commands as $command) {
            try {
                $exitCode = Artisan::call($command['name'], $command['arguments']);
                $output = trim(Artisan::output());

                $executed[] = [
                    'command' => $this->formatCommand($command['name'], $command['arguments']),
                    'exit_code' => $exitCode,
                    'output' => mb_substr($output, 0, 3000),
                ];

                if ($exitCode !== 0) {
                    throw new \RuntimeException("Command [{$command['name']}] returned exit code {$exitCode}.");
                }
            } catch (Throwable $exception) {
                $error = $exception->getMessage();

                Log::error('One-time server bootstrap command failed.', [
                    'command' => $command['name'],
                    'arguments' => $command['arguments'],
                    'error' => $exception->getMessage(),
                ]);

                break;
            }
        }

        return [
            'success' => $error === null,
            'commands' => $executed,
            'error' => $error,
        ];
    }

    private function clearSubdirectoryRouteCacheIfNeeded(): void
    {
        if (! $this->isSubdirectoryDeployment() || ! app()->routesAreCached()) {
            return;
        }

        try {
            Artisan::call('route:clear');
            Log::warning('Route cache was auto-cleared for subdirectory deployment to avoid 405 root requests.', [
                'app_url' => $this->appUrl(),
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to auto-clear route cache for subdirectory deployment.', [
                'app_url' => $this->appUrl(),
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function isSubdirectoryDeployment(): bool
    {
        $path = parse_url($this->appUrl(), PHP_URL_PATH);

        if (! is_string($path)) {
            return false;
        }

        return trim($path, '/') !== '';
    }

    private function appUrl(): string
    {
        $configured = config('app.url');
        if (is_string($configured) && trim($configured) !== '') {
            return trim($configured);
        }

        $fromEnv = $this->envFromDotenv('APP_URL');

        return $fromEnv === null || trim($fromEnv) === ''
            ? 'http://localhost'
            : trim($fromEnv);
    }

    private function formatCommand(string $name, array $arguments): string
    {
        if ($arguments === []) {
            return $name;
        }

        $pairs = [];
        foreach ($arguments as $key => $value) {
            if (is_int($key)) {
                $pairs[] = (string) $value;
                continue;
            }

            if ($value === true) {
                $pairs[] = $key;
                continue;
            }

            $pairs[] = "{$key}={$value}";
        }

        return trim($name.' '.implode(' ', $pairs));
    }

    private function markerPath(): string
    {
        $marker = (string) $this->settingMarkerPath();
        $isAbsolute = str_starts_with($marker, DIRECTORY_SEPARATOR)
            || preg_match('/^[A-Za-z]:[\\\\\\/]/', $marker) === 1;

        return $isAbsolute ? $marker : storage_path($marker);
    }

    private function ensureDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            @mkdir($directory, 0755, true);
        }
    }

    private function settingEnabled(): bool
    {
        return $this->settingBool('enabled', 'BOOTSTRAP_ON_FIRST_REQUEST', true);
    }

    private function settingAbortOnFailure(): bool
    {
        return $this->settingBool('abort_on_failure', 'BOOTSTRAP_ON_FIRST_REQUEST_ABORT', true);
    }

    private function settingSeedEnabled(): bool
    {
        return $this->settingBool('seed', 'BOOTSTRAP_ON_FIRST_REQUEST_SEED', true);
    }

    private function settingStorageLinkForce(): bool
    {
        return $this->settingBool('storage_link_force', 'BOOTSTRAP_ON_FIRST_REQUEST_STORAGE_FORCE', true);
    }

    private function settingMarkerPath(): string
    {
        return $this->settingString('marker_path', 'BOOTSTRAP_ON_FIRST_REQUEST_MARKER', 'app/bootstrap-once.json');
    }

    private function settingBool(string $configKey, string $envKey, bool $default): bool
    {
        $configValue = config("bootstrap.once.{$configKey}");

        if ($configValue !== null) {
            return filter_var($configValue, FILTER_VALIDATE_BOOLEAN);
        }

        $envValue = $this->envFromDotenv($envKey);
        if ($envValue === null || $envValue === '') {
            return $default;
        }

        return filter_var($envValue, FILTER_VALIDATE_BOOLEAN);
    }

    private function settingString(string $configKey, string $envKey, string $default): string
    {
        $configValue = config("bootstrap.once.{$configKey}");
        if (is_string($configValue) && $configValue !== '') {
            return $configValue;
        }

        $envValue = $this->envFromDotenv($envKey);
        if ($envValue === null || trim($envValue) === '') {
            return $default;
        }

        return trim($envValue);
    }

    private function envFromDotenv(string $key): ?string
    {
        $envFile = base_path('.env');
        if (! is_file($envFile) || ! is_readable($envFile)) {
            return null;
        }

        $lines = @file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (! is_array($lines)) {
            return null;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            if (trim($name) !== $key) {
                continue;
            }

            return trim($value, " \t\n\r\0\x0B\"'");
        }

        return null;
    }
}
