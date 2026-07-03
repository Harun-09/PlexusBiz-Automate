<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Normalize Inertia page URL payload so browser history never keeps duplicated
     * subfolder segments like /plexusbiz/public/plexusbiz/public/...
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $response = parent::handle($request, $next);

        $content = $response->getContent();

        if (! is_string($content) || $content === '') {
            return $response;
        }

        if ($request->header('X-Inertia')) {
            $decoded = json_decode($content, true);

            if (is_array($decoded) && isset($decoded['url']) && is_string($decoded['url'])) {
                $normalizedUrl = $this->normalizeInertiaUrl($decoded['url']);

                if ($normalizedUrl !== $decoded['url']) {
                    $decoded['url'] = $normalizedUrl;
                    $response->setContent((string) json_encode($decoded));
                }
            }

            return $response;
        }

        $updatedContent = preg_replace_callback(
            '/data-page="([^"]+)"/',
            function (array $matches): string {
                $encodedPage = $matches[1] ?? '';
                $decodedPage = html_entity_decode($encodedPage, ENT_QUOTES, 'UTF-8');
                $page = json_decode($decodedPage, true);

                if (! is_array($page) || ! isset($page['url']) || ! is_string($page['url'])) {
                    return $matches[0];
                }

                $normalizedUrl = $this->normalizeInertiaUrl($page['url']);

                if ($normalizedUrl === $page['url']) {
                    return $matches[0];
                }

                $page['url'] = $normalizedUrl;
                $reencoded = json_encode($page);

                if (! is_string($reencoded) || $reencoded === '') {
                    return $matches[0];
                }

                return 'data-page="'.htmlspecialchars($reencoded, ENT_QUOTES, 'UTF-8', false).'"';
            },
            $content,
            1
        );

        if (is_string($updatedContent) && $updatedContent !== $content) {
            $response->setContent($updatedContent);
        }

        return $response;
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $supplier = $user?->supplier;
        $b2cCustomer = auth()->guard('b2c')->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => $user->status?->value ?? UserStatus::Active->value,
                    'roles' => $user->getRoleNames()->values(),
                    'permissions' => $user->getAllPermissions()->pluck('name')->values(),
                    'supplier' => $supplier ? [
                        'id' => $supplier->id,
                        'company_name' => $supplier->company_name,
                        'status' => $supplier->status->value,
                    ] : null,
                ] : null,
                'b2c_customer' => $b2cCustomer ? [
                    'id' => $b2cCustomer->id,
                    'name' => $b2cCustomer->name,
                    'email' => $b2cCustomer->email,
                ] : null,
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
        ];
    }

    private function normalizeInertiaUrl(string $url): string
    {
        $parts = parse_url($url);

        if (! is_array($parts)) {
            return $url;
        }

        $path = $parts['path'] ?? null;
        $path = is_string($path) ? $path : '';

        if ($path === '') {
            return $url;
        }

        $normalizedPath = $this->collapseLeadingPublicRepeat($path);

        if ($normalizedPath === $path) {
            return $url;
        }

        $query = $parts['query'] ?? null;
        $query = is_string($query) && $query !== '' ? '?'.$query : '';
        $fragment = $parts['fragment'] ?? null;
        $fragment = is_string($fragment) && $fragment !== '' ? '#'.$fragment : '';

        return $normalizedPath.$query.$fragment;
    }

    private function collapseLeadingPublicRepeat(string $path): string
    {
        $hasTrailingSlash = str_ends_with($path, '/');
        $segments = array_values(array_filter(
            explode('/', trim($path, '/')),
            static fn (string $segment): bool => $segment !== ''
        ));

        if (count($segments) < 4) {
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

        if ($collapsed !== '/' && $hasTrailingSlash) {
            $collapsed .= '/';
        }

        return $collapsed;
    }
}
