<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Spam protection for the public contact endpoints.
 *
 * Responses keep the endpoints' existing JSON shape and HTTP 200, because the
 * front-end submitFroms() handlers ignore non-2xx responses.
 */
class ProtectContactForms
{
    public const HONEYPOT_FIELD = 'aldar_hp';
    public const MAX_SUBMISSIONS = 5;
    public const DECAY_SECONDS = 600;

    /** @var RateLimiter */
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next)
    {
        $route = optional($request->route())->getName();

        if (filled($request->input(self::HONEYPOT_FIELD))) {
            // Route and IP only, never the form body: this log is for spotting false positives.
            Log::info('Contact form submission dropped by the honeypot', ['route' => $route, 'ip' => $request->ip()]);

            return response()->json([
                'success'  => true,
                'disabled' => true,
                'message'  => trans('frontend::main.sendded'),
            ]);
        }

        $key = 'contact-forms:' . $route . ':' . $request->ip();

        if ($this->limiter->tooManyAttempts($key, self::MAX_SUBMISSIONS)) {
            Log::info('Contact form submission refused by the rate limiter', ['route' => $route, 'ip' => $request->ip()]);

            return response()->json([
                'success' => false,
                'message' => trans('frontend::main.too_many_submissions'),
            ]);
        }

        $response = $next($request);

        // Only stored leads count, so typos and validation errors never lock a visitor out.
        if ($response instanceof JsonResponse && data_get($response->getData(true), 'success') === true) {
            $this->limiter->hit($key, self::DECAY_SECONDS);
        }

        return $response;
    }
}
