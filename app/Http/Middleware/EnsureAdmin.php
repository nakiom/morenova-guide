<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->isAdmin()) {
            if ($request->expectsJson()) {
                abort(403, __('messages.unauthorized'));
            }

            return redirect()
                ->route('dashboard')
                ->with('error', __('messages.unauthorized_hint'));
        }

        return $next($request);
    }
}
