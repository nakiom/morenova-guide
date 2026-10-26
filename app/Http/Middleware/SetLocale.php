<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Locales;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);

        // Solo persistir en sesión si ya había preferencia explícita o el usuario eligió idioma.
        // Si no hay sesión de locale, se usa el del navegador sin forzar cookie hasta que elijan.
        if (Session::has('locale') || $request->query('lang') || $request->user()?->preferred_locale) {
            Session::put('locale', $locale);
        }

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        $candidates = [
            Locales::normalize(is_string($request->query('lang')) ? $request->query('lang') : null),
            Locales::normalize(Session::get('locale')),
            Locales::normalize($request->user()?->preferred_locale),
            Locales::normalize($request->getPreferredLanguage(Locales::SUPPORTED)),
            Locales::normalize(config('app.locale', 'es')),
        ];

        foreach ($candidates as $candidate) {
            if ($candidate !== null) {
                return $candidate;
            }
        }

        return 'es';
    }
}
