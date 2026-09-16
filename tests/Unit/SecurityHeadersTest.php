<?php

namespace Tests\Unit;

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Tests\TestCase;

/** S3: security headers on every application response; the CSP is Report-Only. */
class SecurityHeadersTest extends TestCase
{
    public function test_a_response_gets_every_security_header(): void
    {
        $response = (new SecurityHeaders)->handle(Request::create('/'), fn () => response('ok'));

        $this->assertSame('max-age=31536000', $response->headers->get('Strict-Transport-Security'));
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertSame('SAMEORIGIN', $response->headers->get('X-Frame-Options'));
        $this->assertSame('strict-origin-when-cross-origin', $response->headers->get('Referrer-Policy'));
        $this->assertSame(SecurityHeaders::CONTENT_SECURITY_POLICY_REPORT_ONLY, $response->headers->get('Content-Security-Policy-Report-Only'));
        $this->assertFalse($response->headers->has('Content-Security-Policy'));
    }

    public function test_the_middleware_is_global(): void
    {
        $this->assertTrue($this->app->make(Kernel::class)->hasMiddleware(SecurityHeaders::class));
    }
}
