<?php

namespace App\Http\Middleware;

use Fideloper\Proxy\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array|string
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
     * the CDN. (fideloper/proxy's default maps HEADER_X_FORWARDED_FOR to X_FORWARDED_ALL.)
     */
    protected function getTrustedHeaderNames()
    {
        return Request::HEADER_X_FORWARDED_FOR;
    }
}
