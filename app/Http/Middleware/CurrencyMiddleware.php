<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CurrencyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('user_currency')) {
            // Never perform geolocation or exchange-rate HTTP calls in global
            // middleware. They delay every first page view and every crawler.
            Session::put('user_currency', 'GHS');
            Session::put('user_currency_symbol', 'GH₵');
            Session::put('user_exchange_rate', 1.0);
        }

        return $next($request);
    }
}
