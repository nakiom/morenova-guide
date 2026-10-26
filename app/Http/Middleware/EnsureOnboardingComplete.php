<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->needsOnboarding() && ! $request->routeIs(
            'first-login.show',
            'first-login.store',
            'logout',
            'locale.switch',
        )) {
            return redirect()->route('first-login.show');
        }

        return $next($request);
    }
}
