<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR;

    /**
     * Only the client address is taken from proxy headers. Forwarded host, scheme and
     * port stay untrusted even when $proxies is set, so clients cannot spoof them past
     * the CDN.
     */
    protected function getTrustedHeaderNames()
    {
        return Request::HEADER_X_FORWARDED_FOR;
    }
}
