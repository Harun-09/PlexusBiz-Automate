<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
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
        $canonicalUri = $this->canonicalMalformedSubfolderUri($request);

        if ($canonicalUri !== null) {
            return new RedirectResponse($request->getSchemeAndHttpHost().$canonicalUri, 302);
        }

        $this->normalizeMalformedSubfolderRequest($request);

        $basePath = $this->resolveBasePath($request);
        $runtimeRoot = rtrim($request->getSchemeAndHttpHost(), '/').$basePath;

        URL::forceRootUrl($runtimeRoot);
        $this->applyFlexibleAssetUrl($request);

        return $next($request);
    }

    private function canonicalMalformedSubfolderUri(Request $request): ?string
    {
        $host = $this->normalizeHost($request->getHost());

        if ($this->isLocalHost($host)) {
            return null;
        }

        $configuredBase = $this->normalizeBasePath((string) config('app.subfolder_path', ''));
        $requestUri = (string) $request->server->get('REQUEST_URI', '');

        if ($requestUri === '') {
            return null;
        }

        $uriParts = parse_url($requestUri);

        if (! is_array($uriParts)) {
            return null;
        }

        $path = $uriParts['path'] ?? null;
        $path = is_string($path) ? $path : '';

        if ($path === '') {
            return null;
        }

        $normalizedPath = $this->normalizeDuplicatedPath($path, $configuredBase);

        if ($normalizedPath === $path) {
            return null;
        }

        $queryString = $uriParts['query'] ?? null;
        $queryString = is_string($queryString) ? $queryString : '';

        return $normalizedPath.($queryString === '' ? '' : '?'.$queryString);
    }

    private function normalizeMalformedSubfolderRequest(Request $request): void
    {
        $host = $this->normalizeHost($request->getHost());

        if ($this->isLocalHost($host)) {
            return;
        }

        $configuredBase = $this->normalizeBasePath((string) config('app.subfolder_path', ''));
        $requestUri = (string) $request->server->get('REQUEST_URI', '');

        if ($requestUri === '') {
            return;
        }

        $uriParts = parse_url($requestUri);

        if (! is_array($uriParts)) {
            return;
        }

        $path = $uriParts['path'] ?? null;
        $path = is_string($path) ? $path : '';

        if ($path === '') {
            return;
        }

        $normalizedPath = $this->normalizeDuplicatedPath($path, $configuredBase);

        if ($configuredBase !== '') {
            $publicBase = rtrim($configuredBase, '/').'/public';
        } else {
            $publicBase = $this->extractPublicBaseFromPath($normalizedPath) ?? '';
        }

        $isPublicBaseRequest = $publicBase !== '' && (
            $normalizedPath === $publicBase
            || str_starts_with($normalizedPath, $publicBase.'/')
        );

        if (! $isPublicBaseRequest) {
            return;
        }

        $scriptName = rtrim($publicBase, '/').'/index.php';
        $publicIndexFile = str_replace('\\', '/', public_path('index.php'));
        $currentScriptName = (string) $request->server->get('SCRIPT_NAME', '');
        $currentPhpSelf = (string) $request->server->get('PHP_SELF', '');
        $currentScriptFilename = (string) $request->server->get('SCRIPT_FILENAME', '');
        $needsRewrite = $normalizedPath !== $path
            || $currentScriptName !== $scriptName
            || $currentPhpSelf !== $scriptName
            || ! str_ends_with(str_replace('\\', '/', $currentScriptFilename), '/public/index.php');

        if (! $needsRewrite) {
            return;
        }

        $queryString = $uriParts['query'] ?? null;
        $queryString = is_string($queryString) ? $queryString : '';
        $normalizedUri = $normalizedPath.($queryString === '' ? '' : '?'.$queryString);
        $server = $request->server->all();

        $server['REQUEST_URI'] = $normalizedUri;
        $server['SCRIPT_NAME'] = $scriptName;
        $server['PHP_SELF'] = $scriptName;
        $server['SCRIPT_FILENAME'] = $publicIndexFile;
        $server['QUERY_STRING'] = $queryString;
        $server['ORIG_PATH_INFO'] = $normalizedPath;

        // Reset Symfony request URI/path caches after rewriting server values.
        $request->initialize(
            $request->query->all(),
            $request->request->all(),
            $request->attributes->all(),
            $request->cookies->all(),
            $request->files->all(),
            $server,
            $request->getContent(),
        );
    }

    private function collapseDuplicatedPublicSegments(string $path, string $publicBase): string
    {
        $base = '/'.trim($publicBase, '/');
        $baseTail = ltrim($base, '/');
        $patterns = [
            $base.'/index.php/'.$baseTail.'/index.php',
            $base.'/index.php/'.$baseTail,
            $base.'/'.$baseTail.'/index.php',
            $base.'/'.$baseTail,
        ];

        do {
            $updated = false;

            foreach ($patterns as $pattern) {
                if ($path === $pattern || str_starts_with($path, $pattern.'/')) {
                    $suffix = substr($path, strlen($pattern));
                    $path = $base.$suffix;
                    $updated = true;
                }
            }
        } while ($updated);

        return $path;
    }

    private function normalizeDuplicatedPath(string $path, string $configuredBase): string
    {
        $normalized = $path;

        if ($configuredBase !== '') {
            $publicBase = rtrim($configuredBase, '/').'/public';
            $normalized = $this->collapseDuplicatedPublicSegments($normalized, $publicBase);
        }

        return $this->collapseLeadingPublicRepeat($normalized);
    }

    private function collapseLeadingPublicRepeat(string $path): string
    {
        $isTrailingSlash = str_ends_with($path, '/');
        $segments = array_values(array_filter(explode('/', trim($path, '/')), static fn ($segment) => $segment !== ''));
        $count = count($segments);

        if ($count < 4) {
            return $path;
        }

        do {
            $updated = false;
            $count = count($segments);

            for ($len = intdiv($count, 2); $len >= 2; $len--) {
                $head = array_slice($segments, 0, $len);
                $next = array_slice($segments, $len, $len);

                if ($head === $next && in_array('public', $head, true)) {
                    $segments = array_merge($head, array_slice($segments, $len * 2));
                    $updated = true;
                    break;
                }
            }
        } while ($updated);

        $collapsed = '/'.implode('/', $segments);

        if ($collapsed !== '/' && $isTrailingSlash) {
            $collapsed .= '/';
        }

        return $collapsed;
    }

    private function extractPublicBaseFromPath(string $path): ?string
    {
        $segments = array_values(array_filter(explode('/', trim($path, '/')), static fn ($segment) => $segment !== ''));

        foreach ($segments as $index => $segment) {
            if ($segment === 'public' && $index >= 1) {
                return '/'.implode('/', array_slice($segments, 0, $index + 1));
            }
        }

        return null;
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
        $requestBasePath = $this->normalizeBasePath((string) $request->getBasePath(), false);
        $host = $this->normalizeHost($request->getHost());
        $requestBasePath = $this->collapseLeadingPublicRepeat($requestBasePath);

        // Some shared-hosting setups only expose the app through ".../public".
        // Preserve that runtime base so generated URLs do not drop "/public".
        if ($requestBasePath !== '' && str_ends_with($requestBasePath, '/public') && ! $this->isLocalHost($host)) {
            return $requestBasePath;
        }

        $configured = $this->normalizeBasePath((string) config('app.subfolder_path', ''));

        if ($configured !== '' && ! $this->isLocalHost($host)) {
            $requestUriPath = parse_url((string) $request->getRequestUri(), PHP_URL_PATH);
            $requestUriPath = is_string($requestUriPath) ? '/'.ltrim($requestUriPath, '/') : '';
            $configuredPublicBase = rtrim($configured, '/').'/public';

            if ($requestUriPath !== '' && ($requestUriPath === $configuredPublicBase || str_starts_with($requestUriPath, $configuredPublicBase.'/'))) {
                return $configuredPublicBase;
            }

            return $configured;
        }

        $forwardedPrefix = $this->normalizeBasePath((string) $request->headers->get('x-forwarded-prefix', ''));

        if ($forwardedPrefix !== '') {
            return $forwardedPrefix;
        }

        return $requestBasePath;
    }

    private function normalizeBasePath(string $value, bool $stripPublic = true): string
    {
        $trimmed = trim($value);

        if ($trimmed === '' || $trimmed === '/') {
            return '';
        }

        $normalized = '/'.trim($trimmed, '/');

        if ($stripPublic && $normalized === '/public') {
            return '';
        }

        if ($stripPublic && str_ends_with($normalized, '/public')) {
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
