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
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => __('messages.page_expired')], 419);
            }

            $fallback = route('dashboard');
            $patient = $request->route('patient');
            if ($request->routeIs('clinical.sessions.store') && $patient) {
                $fallback = route('clinical.sessions.create', $patient);
            }

            return redirect()
                ->back(fallback: $fallback)
                ->withInput($request->except('_token', 'password', 'password_confirmation', 'current_password'))
                ->with('error', __('messages.page_expired_hint'));
        });
    })->create();
