<?php

namespace App\Http\Middleware;

use Closure;

class AbortPublic
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // if(\Illuminate\Support\Str::contains($request->root(), '/public'))
        // {
        //     return redirect(\Illuminate\Support\Str::replaceFirst('/public', '', $request->url()));
        // }

        // if(\Illuminate\Support\Str::of($request->url())->endsWith([ '/tr']) || \Illuminate\Support\Str::contains($request->url(), ['/tr/']))
        // {
        //     abort(404);
        // }

        
        return $next($request);
    }
}
