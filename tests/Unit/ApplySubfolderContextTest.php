<?php

namespace Tests\Unit;

use App\Http\Middleware\ApplySubfolderContext;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ApplySubfolderContextTest extends TestCase
{
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
    }
}
