<?php

declare(strict_types=1);

namespace App\Http\Controllers\Center;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CredentialController extends Controller
{
    public function index(Request $request): View
    {
        $manager = $this->manager($request);
        $q = trim((string) $request->get('q', ''));

        $users = User::query()
            ->where(function ($query) use ($manager) {
                $query->where('id', $manager->id)
                    ->orWhere('parent_user_id', $manager->id);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('job_title', 'like', "%{$q}%");
                });
            })
            ->orderByRaw('parent_user_id IS NOT NULL')
            ->orderBy('name')
            ->get();

        return view('center.credentials.index', compact('users', 'manager', 'q'));
    }

    public function edit(Request $request, User $user): View
    {
        $manager = $this->manager($request);
        $this->authorizeUser($manager, $user);

        return view('center.credentials.edit', compact('user', 'manager'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $manager = $this->manager($request);
        $this->authorizeUser($manager, $user);

        $rules = [
            'password' => ['required', 'confirmed', Password::defaults()],
        ];

        if ($user->is($manager)) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $validated = $request->validate($rules);

        $updates = [
            'password' => $validated['password'],
            'remember_token' => null,
        ];

        // Si el responsable cambia la clave de un subusuario, exigir nuevo onboarding (clave + privacidad).
        if (! $user->is($manager)) {
            $updates['must_change_password'] = true;
            $updates['privacy_accepted_at'] = null;
        }

        $user->forceFill($updates)->save();

        return redirect()
            ->route('center.credentials.index')
            ->with('success', __('messages.password_updated'));
    }

    private function manager(Request $request): User
    {
        /** @var User $user */
        $user = $request->user();

        abort_if($user->isAdmin() || $user->isSubUser() || !$user->center_id, 403);

        return $user;
    }

    private function authorizeUser(User $manager, User $user): void
    {
        $canManage = $user->is($manager)
            || ($user->center_id === $manager->center_id && $user->parent_user_id === $manager->id);

        abort_unless($canManage, 403);
    }
}
