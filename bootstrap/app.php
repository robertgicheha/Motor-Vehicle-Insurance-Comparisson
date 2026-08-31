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
    ->withProviders([
        \App\Providers\AuthServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        // Render (like Heroku/Railway) terminates TLS at its edge and forwards
        // plain HTTP to the container with an X-Forwarded-Proto: https header.
        // Without trusting that header, Laravel thinks every request is plain
        // HTTP and generates http:// URLs for every asset/route — which the
        // browser then blocks as mixed content on the https:// page. The
        // container isn't directly internet-reachable (only the platform's own
        // edge can reach it), so trusting all incoming proxy headers is safe
        // here, matching the standard setup for apps behind a managed PaaS.
        $middleware->trustProxies(at: '*');
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->alias([
            'online' => \App\Http\Middleware\LogUserOnline::class,
            'log.activity' => \App\Http\Middleware\LogActivity::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'recaptcha' => \App\Http\Middleware\ValidateRecaptcha::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
