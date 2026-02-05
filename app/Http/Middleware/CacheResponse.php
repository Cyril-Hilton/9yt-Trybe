<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $ttl = 600): Response
    {
        // Only cache GET requests
        if (!$request->isMethod('get')) {
            return $next($request);
        }

        // Do not cache for authenticated users
        if (Auth::check() || Auth::guard('company')->check() || Auth::guard('admin')->check()) {
            return $next($request);
        }

        // Generate cache key based on URL and query parameters
        $key = 'route_cache:' . $request->fullUrl();

        if (Cache::has($key)) {
            $content = Cache::get($key);
            return response($content);
        }

        $response = $next($request);

        // Only cache successful responses
        if ($response->getStatusCode() === 200) {
            $content = $response->getContent();
            Cache::put($key, $content, now()->addSeconds($ttl));
        }

        return $response;
    }
}
