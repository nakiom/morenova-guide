<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED = ['es', 'en', 'pt', 'zh'];

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);
        Session::put('locale', $locale);

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        $candidates = [
            $request->query('lang'),
            Session::get('locale'),
            $request->getPreferredLanguage(self::SUPPORTED),
            config('app.locale', 'es'),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && in_array($candidate, self::SUPPORTED, true)) {
                return $candidate;
            }
        }

        return 'es';
    }
}
