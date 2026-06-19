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

        // Keep keys short and deterministic across cache drivers.
        $key = 'route_cache:' . sha1($request->fullUrl());
        $cached = Cache::get($key);

        if (is_array($cached) && array_key_exists('content', $cached)) {
            return response(
                $cached['content'],
                $cached['status'] ?? 200,
                array_filter([
                    'Content-Type' => $cached['content_type'] ?? null,
                    'X-Page-Cache' => 'HIT',
                ])
            );
        }

        $response = $next($request);

        // Only cache successful responses
        if ($response->getStatusCode() === 200) {
            Cache::put($key, [
                'content' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'content_type' => $response->headers->get('Content-Type'),
            ], now()->addSeconds($ttl));

            $response->headers->set('X-Page-Cache', 'MISS');
        }

        return $response;
    }
}
