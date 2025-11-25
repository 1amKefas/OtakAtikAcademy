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

        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'auth' => Illuminate\Auth\Middleware\Authenticate::class,
            'auth.basic' => Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers' => Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => Illuminate\Auth\Middleware\Authorize::class,
            'guest' => Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => Illuminate\Auth\Middleware\RequirePassword::class,
            'precognitive' => Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            'signed' => Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'admin' => App\Http\Middleware\AdminMiddleware::class,
            'instructor' => App\Http\Middleware\InstructorMiddleware::class, // TAMBAH INI!
        ]);

        $middleware->web(append: [
            Illuminate\Cookie\Middleware\EncryptCookies::class,
            Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            Illuminate\Session\Middleware\StartSession::class,
            Illuminate\View\Middleware\ShareErrorsFromSession::class,
            Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->api(prepend: [
            Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            Illuminate\Routing\Middleware\SubstituteBindings::class,
            Illuminate\Session\Middleware\StartSession::class,
            Illuminate\Cookie\Middleware\EncryptCookies::class,
            Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();