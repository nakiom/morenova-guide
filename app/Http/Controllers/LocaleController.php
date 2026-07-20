<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    private const SUPPORTED = ['es', 'en', 'pt', 'zh'];

    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, self::SUPPORTED, true), 404);

        Session::put('locale', $locale);
        App::setLocale($locale);

        $previous = url()->previous();
        $fallback = route('home');

        if (!$previous || $previous === $request->fullUrl()) {
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
