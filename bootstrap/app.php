<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.simple'    => \App\Http\Middleware\SimpleAuth::class,
            'require.onboarding' => \App\Http\Middleware\RequireOnboarding::class,
            'require.subscription' => \App\Http\Middleware\RequireSubscription::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
        // Midtrans posts to the webhook without a CSRF token.
        $middleware->validateCsrfTokens(except: [
            'subscription/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
