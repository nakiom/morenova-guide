<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class FirstLoginController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        if (! $user->needsOnboarding()) {
            return redirect()->route('dashboard');
        }

        return view('auth.first-login', [
            'user' => $user,
            'mustChangePassword' => (bool) $user->must_change_password,
            'mustAcceptPrivacy' => $user->privacy_accepted_at === null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        if (! $user->needsOnboarding()) {
            return redirect()->route('dashboard');
        }

        $mustChangePassword = (bool) $user->must_change_password;
        $mustAcceptPrivacy = $user->privacy_accepted_at === null;

        $rules = [];
        $messages = [];

        if ($mustAcceptPrivacy) {
            $rules['privacy_accepted'] = ['accepted'];
            $messages['privacy_accepted.accepted'] = __('messages.first_login_privacy_required');
        }
        if ($mustChangePassword) {
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        $validated = $request->validate($rules, $messages);

        $updates = [];

        if ($mustAcceptPrivacy) {
            $updates['privacy_accepted_at'] = now();
        }

        if ($mustChangePassword) {
            $updates['password'] = $validated['password'];
            $updates['must_change_password'] = false;
            $updates['remember_token'] = null;

            if (Hash::check($validated['password'], $user->password)) {
                return back()
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->withErrors([
                        'password' => __('messages.first_login_password_different'),
                    ]);
            }
        }

        $user->forceFill($updates)->save();

        $request->session()->forget('url.intended');

        return redirect()
            ->route('dashboard')
            ->with('success', __('messages.first_login_completed'));
    }
}
