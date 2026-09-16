<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Security headers on every response the application sends (spec S3).
 *
 * Registered first in the global middleware stack, so pages, redirects, JSON, /img images and rendered
 * exceptions all carry them. Files the web server serves directly (compiled CSS/JS, uploads) never reach PHP.
 *
 * HSTS has no includeSubDomains: other hosts share the Hostinger account. The Content-Security-Policy is
 * Report-Only, so browsers log violations and block nothing. It has no report-uri, because the app has no
 * endpoint or worker to collect reports. It lists the external origins the views load today; the views use
 * inline scripts without nonces, hence 'unsafe-inline' and 'unsafe-eval'.
 *
 * Two origins beyond research A's grep-based list were confirmed live, in a real browser, by Report-Only
 * console violations on the public homepage (the grep missed them because neither is a literal `https://`
 * string in a Blade file):
 *  - `flags.fmcdn.net` (img-src): the phone-country picker (`data-thumbnail="{{ $country->flag }}"`,
 *    e.g. Modules/Frontend/Resources/views/pages/contact_us.blade.php) gets flag URLs from PHP country
 *    data, not a template literal.
 *  - `www.google.com` (frame-src): `maps.google.com/maps/api/js` (loaded for the admin location picker,
 *    Modules/Cms/Resources/views/components/inputs/locationSelector.blade.php) is the Maps JS API script
 *    host, but the GMaps widget it builds renders its interactive map in an iframe on `www.google.com`,
 *    a different host from the one that served the script.
 */
class SecurityHeaders
{
    public const HEADERS = [
        'Strict-Transport-Security' => 'max-age=31536000',
        'X-Content-Type-Options'    => 'nosniff',
        'X-Frame-Options'           => 'SAMEORIGIN',
        'Referrer-Policy'           => 'strict-origin-when-cross-origin',
    ];

    public const CONTENT_SECURITY_POLICY_REPORT_ONLY =
        "default-src 'self'; "
        . "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdnjs.cloudflare.com cdn.jsdelivr.net unpkg.com www.amcharts.com "
        . "maxcdn.bootstrapcdn.com stackpath.bootstrapcdn.com cdn.ampproject.org cdn.tiny.cloud maps.google.com www.gstatic.com; "
        . "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net unpkg.com maxcdn.bootstrapcdn.com stackpath.bootstrapcdn.com "
        . "fonts.googleapis.com ampbyexample.com; "
        . "font-src 'self' data: fonts.gstatic.com; "
        . "img-src 'self' data: kq9v7r75.rocketcdn.com ampbyexample.com flags.fmcdn.net; "
        . "frame-src 'self' www.youtube.com maps.google.com www.google.com; "
        . "connect-src 'self'";

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        foreach (self::HEADERS as $name => $value) {
            $response->headers->set($name, $value);
        }
        $response->headers->set('Content-Security-Policy-Report-Only', self::CONTENT_SECURITY_POLICY_REPORT_ONLY);

        return $response;
    }
}
