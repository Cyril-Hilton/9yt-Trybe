<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(function (Request $request) {
            // Check which guard should be used based on the URL
            if ($request->is('organization/*')) {
                return route('organization.login');
            }

            if ($request->is('admin/*')) {
                return route('admin.login');
            }

            // Default: redirect to user login for ticket buyers
            return route('user.login');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            // Redirect authenticated users to their appropriate dashboard
            if ($request->is('admin/*') || $request->is('admin')) {
                return route('admin.dashboard');
            }

            if ($request->is('organization/*') || $request->is('organization')) {
                return route('organization.dashboard');
            }

            // Default: redirect to user dashboard
            return route('user.dashboard');
        });

        $middleware->alias([
            'auth.company' => \App\Http\Middleware\CompanyAuth::class,
            'auth.admin' => \App\Http\Middleware\AdminAuth::class,
            'cache.response' => \App\Http\Middleware\CacheResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (TokenMismatchException $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Session expired. Please refresh the page.',
                ], 419);
            }

            if ($request->is('organization/*')) {
                return redirect()
                    ->route('organization.login')
                    ->with('error', 'Session expired. Please log in again.');
            }

            if ($request->is('admin/*')) {
                return redirect()
                    ->route('admin.login')
                    ->with('error', 'Session expired. Please log in again.');
            }

            if ($request->is('staff/*')) {
                return redirect()
                    ->route('staff.login')
                    ->with('error', 'Session expired. Please log in again.');
            }

            // For public users/buyers, just redirect them back to where they were or home,
            // with a session expired message so they can continue browsing or log back in gracefully.
            return redirect()
                ->back()->fallback(route('home'))
                ->with('error', 'Your session has expired. Please try again.');
        });
    })->create();
