<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(Locales::isSupported($locale), 404);

        Session::put('locale', $locale);
        App::setLocale($locale);

        $user = $request->user();
        if ($user) {
            $user->forceFill(['preferred_locale' => $locale])->save();
        }

        $previous = url()->previous();
        $fallback = route('home');

        if (! $previous || $previous === $request->fullUrl()) {
            return redirect($fallback);
        }

        $parts = parse_url($previous);
        $path = ($parts['path'] ?? '/') ?: '/';
        parse_str($parts['query'] ?? '', $query);
        unset($query['lang']);

        $target = $path;
        if ($query !== []) {
            $target .= '?'.http_build_query($query);
        }

        return redirect($target);
    }
}
