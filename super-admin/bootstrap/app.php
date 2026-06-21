<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(
            fn (Request $request) => route('artist-studio.login'),
        );

        $middleware->alias([
            'artist' => \App\Http\Middleware\EnsureArtist::class,
        ]);

        // Provider webhooks live outside the CSRF / session world.
        $middleware->validateCsrfTokens(except: [
            'webhooks/stripe',
            'webhooks/mtn',
            'webhooks/airtel',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (PostTooLargeException $exception, Request $request) {
            if ($request->is('super-admin/assets', 'super-admin/assets/*')) {
                return back()->withErrors([
                    'cover' => 'The upload is too large. Cover images must be 5 MB or smaller.',
                ]);
            }

            return null;
        });
    })->create();
