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
    ->withMiddleware(function (Middleware $middleware): void {
        // Detrás de proxy/SSL (subdominio dematech, Cloudflare, etc.).
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
            'onboarding' => \App\Http\Middleware\EnsureOnboardingComplete::class,
        ]);
        // Evita 419 al cerrar sesión con token CSRF vencido (página abierta mucho rato).
        $middleware->validateCsrfTokens(except: [
            'logout',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage() ?: __('messages.unauthorized')], 403);
            }

            if ($request->user()) {
                return redirect()
                    ->route('dashboard')
                    ->with('error', __('messages.unauthorized_hint'));
            }

            return null;
        });

        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->is('logout') || $request->routeIs('logout')) {
                \Illuminate\Support\Facades\Auth::guard('web')->logout();

                if ($request->hasSession()) {
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }

                return redirect('/');
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => __('messages.page_expired')], 419);
            }

            return response()->view('errors.419', [], 419);
        });
    })->create();
