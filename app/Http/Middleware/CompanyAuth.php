<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CompanyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('company')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }

            return redirect()->route('organization.login')->with('error', 'Please login to access this area.');
        }

        $company = Auth::guard('company')->user();

        if ($company->is_suspended) {
            Auth::guard('company')->logout();
            return redirect()->route('organization.login')->with('error', 'Your account has been suspended. Please contact support.');
        }

        return $next($request);
    }
}
