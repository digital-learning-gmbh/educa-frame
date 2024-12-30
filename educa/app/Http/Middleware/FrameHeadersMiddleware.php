<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;

class FrameHeadersMiddleware
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
        $response = $next($request);
        if(method_exists ($response,'header')) {
        $response->header('Content-Security-Policy', "frame-ancestors 'self' ".config('stupla.cors').";");
        $response->header('X-Frame-Options', "ALLOW FROM ".config('stupla.crossdomain'));
        }
        return $response;
    }
}
