<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware aliases
        $middleware->alias([
            'locale' => \App\Http\Middleware\SetLocale::class,
            'allowIPRequest' => \App\Http\Middleware\AllowIPRequest::class,
            'auth.api' => \App\Http\Middleware\AuthenticateApi::class,
        ]);

        // Add SetLocale to web middleware group
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Configure redirectGuestsTo - Laravel automatically excludes routes with 'guest' middleware
        // So login/register routes won't trigger redirect, preventing loops
        $middleware->redirectGuestsTo('/login');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle authentication exceptions for web requests
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $exception, \Illuminate\Http\Request $request) {
            if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                // Only redirect web requests, not API requests
                if (!$request->expectsJson() && !$request->is('api/*')) {
                    // Don't redirect if already on login/register pages to prevent loops
                    $path = $request->path();
                    if (!str_starts_with($path, 'login') && !str_starts_with($path, 'register')) {
                        // Use path instead of route name to avoid route resolution issues
                        return redirect('/login')
                            ->with('error', 'Please login to access this page.');
                    }
                }
            }
            return $response;
        });

        Integration::handles($exceptions);
    })->create();
