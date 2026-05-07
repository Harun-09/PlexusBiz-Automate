<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ApplySubfolderContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $basePath = $this->resolveBasePath($request);
        $runtimeRoot = rtrim($request->getSchemeAndHttpHost(), '/').$basePath;

        URL::forceRootUrl($runtimeRoot);
        $this->applyFlexibleAssetUrl($request);

        return $next($request);
    }

    private function applyFlexibleAssetUrl(Request $request): void
    {
        $configuredAssetUrl = trim((string) config('app.asset_url', ''));

        if ($configuredAssetUrl === '') {
            return;
        }

        $requestHost = $this->normalizeHost($request->getHost());

        if (! $this->isLocalHost($requestHost)) {
            return;
        }

        $assetHost = $this->extractHostFromUrl($configuredAssetUrl);

        if ($assetHost === '' || $this->isLocalHost($assetHost)) {
            return;
        }

        config(['app.asset_url' => null]);
    }

    private function resolveBasePath(Request $request): string
    {
        $configured = $this->normalizeBasePath((string) config('app.subfolder_path', ''));

        if ($configured !== '') {
            return $configured;
        }

        $forwardedPrefix = $this->normalizeBasePath((string) $request->headers->get('x-forwarded-prefix', ''));

        if ($forwardedPrefix !== '') {
            return $forwardedPrefix;
        }

        return $this->normalizeBasePath($request->getBasePath());
    }

    private function normalizeBasePath(string $value): string
    {
        $trimmed = trim($value);

        if ($trimmed === '' || $trimmed === '/') {
            return '';
        }

        $normalized = '/'.trim($trimmed, '/');

        if ($normalized === '/public') {
            return '';
        }

        if (str_ends_with($normalized, '/public')) {
            $normalized = substr($normalized, 0, -7);
        }

        return $normalized === '' ? '' : $normalized;
    }

    private function extractHostFromUrl(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return '';
        }

        return $this->normalizeHost($host);
    }

    private function normalizeHost(string $host): string
    {
        return trim(strtolower($host), "[] \t\n\r\0\x0B");
    }

    private function isLocalHost(string $host): bool
    {
        return in_array($host, ['localhost', '127.0.0.1', '::1'], true)
            || str_ends_with($host, '.localhost')
            || str_ends_with($host, '.test');
    }
}
