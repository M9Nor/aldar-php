<?php

namespace Tests\Unit;

use App\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * Production sits behind Hostinger's CDN, so $proxies may become '*' (Task 11) so that
 * $request->ip() reports the visitor's address instead of the CDN's. This guards that,
 * even with $proxies = '*', only X-Forwarded-For is trusted: a client cannot spoof the
 * host, scheme or port that reach the application past the CDN.
 */
class TrustProxiesTest extends TestCase
{
    protected function tearDown(): void
    {
        // The trusted-proxy state lives in Symfony Request static properties; reset it
        // to the framework default so it cannot leak into any other test.
        Request::setTrustedProxies([], -1);

        parent::tearDown();
    }

    public function test_only_the_client_address_is_trusted_from_proxy_headers(): void
    {
        $request = Request::create('http://aldar-emlak.com/', 'GET', [], [], [], [
            'REMOTE_ADDR' => '10.0.0.1',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.7',
            'HTTP_X_FORWARDED_HOST' => 'evil.example',
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PORT' => '8443',
        ]);

        // The committed class keeps $proxies null; force it to '*' here only, the way
        // Task 11 Step 2 will for production behind the CDN.
        $middleware = new class($this->app['config']) extends TrustProxies {
            protected $proxies = '*';
        };

        $nextWasCalled = false;

        $middleware->handle($request, function ($request) use (&$nextWasCalled) {
            $nextWasCalled = true;

            $this->assertSame('203.0.113.7', $request->ip());
            $this->assertSame('aldar-emlak.com', $request->getHost());
            $this->assertFalse($request->isSecure());
            $this->assertSame(80, $request->getPort());

            return new Response();
        });

        $this->assertTrue($nextWasCalled, 'The $next closure inside the middleware was never invoked.');
    }
}
