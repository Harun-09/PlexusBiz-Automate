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
        if (! config('bootstrap.once.enabled') || app()->runningInConsole()) {
            return $next($request);
        }

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

                file_put_contents(
                    $markerPath,
                    json_encode([
                        'attempted_at' => now()->toIso8601String(),
                        'app_env' => app()->environment(),
                        'success' => $result['success'],
                        'commands' => $result['commands'],
                        'error' => $result['error'],
                    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                );

                if (! $result['success'] && config('bootstrap.once.abort_on_failure', true)) {
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
                'arguments' => config('bootstrap.once.storage_link_force', true) ? ['--force' => true] : [],
            ],
            ['name' => 'optimize:clear', 'arguments' => []],
        ];

        if (config('bootstrap.once.migrate_fresh', false)) {
            $arguments = ['--force' => true];
            if (config('bootstrap.once.seed', true)) {
                $arguments['--seed'] = true;
            }

            $commands[] = ['name' => 'migrate:fresh', 'arguments' => $arguments];
        } else {
            $commands[] = ['name' => 'migrate', 'arguments' => ['--force' => true]];

            if (config('bootstrap.once.seed', true)) {
                $commands[] = ['name' => 'db:seed', 'arguments' => ['--force' => true]];
            }
        }

        $commands[] = ['name' => 'optimize', 'arguments' => []];
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
        $marker = (string) config('bootstrap.once.marker_path', 'app/bootstrap-once.json');
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
}
