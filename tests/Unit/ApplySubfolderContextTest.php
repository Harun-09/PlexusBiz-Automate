<?php

namespace Tests\Unit;

use App\Http\Middleware\ApplySubfolderContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ApplySubfolderContextTest extends TestCase
{
    protected function tearDown(): void
    {
        URL::forceRootUrl(null);

        parent::tearDown();
    }

    public function test_local_request_disables_remote_asset_url(): void
    {
        config([
            'app.subfolder_path' => '/plexusbiz',
            'app.asset_url' => 'http://harun.intelsofts.com/plexusbiz',
        ]);

        $middleware = new ApplySubfolderContext();
        $request = Request::create('http://127.0.0.1:8000/login', 'GET');

        $middleware->handle($request, function () {
            return new Response('ok', 200);
        });

        $this->assertNull(config('app.asset_url'));
        $this->assertSame('http://127.0.0.1:8000/login', url('/login'));
    }

    public function test_server_request_keeps_configured_asset_url(): void
    {
        config([
            'app.subfolder_path' => '/plexusbiz',
            'app.asset_url' => 'http://harun.intelsofts.com/plexusbiz',
        ]);

        $middleware = new ApplySubfolderContext();
        $request = Request::create('http://harun.intelsofts.com/plexusbiz/login', 'GET');

        $middleware->handle($request, function () {
            return new Response('ok', 200);
        });

        $this->assertSame('http://harun.intelsofts.com/plexusbiz', config('app.asset_url'));
        $this->assertSame('http://harun.intelsofts.com/plexusbiz/login', url('/login'));
    }

    public function test_server_request_preserves_public_base_when_runtime_is_public_subfolder(): void
    {
        config([
            'app.subfolder_path' => '/plexusbiz',
            'app.asset_url' => 'http://harun.intelsofts.com/plexusbiz',
        ]);

        $middleware = new ApplySubfolderContext();
        $request = Request::create('http://harun.intelsofts.com/plexusbiz/public/login', 'GET');

        $middleware->handle($request, function () {
            return new Response('ok', 200);
        });

        $this->assertSame('/login', $request->getPathInfo());
        $this->assertSame('http://harun.intelsofts.com/plexusbiz/public/login', url('/login'));
    }

    public function test_server_request_collapses_duplicated_public_segments(): void
    {
        config([
            'app.subfolder_path' => '/plexusbiz',
            'app.asset_url' => 'http://harun.intelsofts.com/plexusbiz',
        ]);

        $middleware = new ApplySubfolderContext();
        $request = Request::create('http://harun.intelsofts.com/plexusbiz/public/plexusbiz/public/login', 'GET');
        $calledNext = false;

        $response = $middleware->handle($request, function () use (&$calledNext) {
            $calledNext = true;
            return new Response('ok', 200);
        });

        $this->assertFalse($calledNext);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://harun.intelsofts.com/plexusbiz/public/login', $response->headers->get('Location'));
    }

    public function test_server_request_collapses_duplicated_public_segments_with_index_php_and_query(): void
    {
        config([
            'app.subfolder_path' => '/plexusbiz',
            'app.asset_url' => 'http://harun.intelsofts.com/plexusbiz',
        ]);

        $middleware = new ApplySubfolderContext();
        $request = Request::create(
            'http://harun.intelsofts.com/plexusbiz/public/index.php/plexusbiz/public/index.php/login?from=test',
            'GET'
        );
        $calledNext = false;

        $response = $middleware->handle($request, function () use (&$calledNext) {
            $calledNext = true;
            return new Response('ok', 200);
        });

        $this->assertFalse($calledNext);
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://harun.intelsofts.com/plexusbiz/public/login?from=test', $response->headers->get('Location'));
    }
}
