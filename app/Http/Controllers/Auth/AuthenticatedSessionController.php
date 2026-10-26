<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\Locales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        if (Locales::isSupported($user?->preferred_locale)) {
            Session::put('locale', $user->preferred_locale);
            app()->setLocale($user->preferred_locale);
        }

        if ($user?->needsOnboarding()) {
            return redirect()->route('first-login.show');
        }

        return redirect()->to($this->safeIntendedUrl($request, $user));
    }

    /**
     * Prefer the intended URL after login, but never bounce into routes
     * the user cannot access (e.g. /admin for non-admins).
     */
    private function safeIntendedUrl(Request $request, mixed $user): string
    {
        $default = route('dashboard', absolute: false);
        $intended = $request->session()->pull('url.intended');

        if (! is_string($intended) || $intended === '') {
            return $default;
        }

        $path = parse_url($intended, PHP_URL_PATH) ?: '';

        if (! $user?->isAdmin() && str_starts_with($path, '/admin')) {
            return $default;
        }

        // Absolute URLs to other hosts are ignored.
        $host = parse_url($intended, PHP_URL_HOST);
        if ($host && $host !== $request->getHost()) {
            return $default;
        }

        return $intended;
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
