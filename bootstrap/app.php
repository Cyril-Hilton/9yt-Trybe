<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

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
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();