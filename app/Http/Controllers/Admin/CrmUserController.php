<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CrmUserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with(['center', 'parent'])
            ->where('is_admin', false)
            ->latest();

        if ($request->filled('center_id')) {
            $query->where('center_id', $request->center_id);
        }

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $users = $query->paginate(20)->withQueryString();
        $centers = Center::orderBy('name')->get();

        return view('admin.crm.users.index', compact('users', 'centers'));
    }

    public function create(Request $request): View
    {
        $centers = Center::where('is_active', true)->orderBy('name')->get();
        $centerId = $request->get('center_id');
        $primaryUsers = User::whereNull('parent_user_id')
            ->where('is_admin', false)
            ->when($centerId, fn ($q) => $q->where('center_id', $centerId))
            ->orderBy('name')
            ->get();

        return view('admin.crm.users.create', compact('centers', 'primaryUsers', 'centerId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, null);
        unset($data['photo']);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('user-photos', 'public');
            $data['photo_url'] = null;
        }

        $data['email_verified_at'] = now();
        $data['is_admin'] = false;

        $user = User::create($data);

        return redirect()
            ->route('admin.crm-users.show', $user)
            ->with('success', __('messages.crm_user_created'));
    }

    public function show(User $crm_user): View
    {
        abort_if($crm_user->isAdmin(), 404);

        $crm_user->load(['center', 'parent', 'subUsers', 'equipment']);

        return view('admin.crm.users.show', ['user' => $crm_user]);
    }

    public function edit(User $crm_user): View
    {
        abort_if($crm_user->isAdmin(), 404);

        $centers = Center::where('is_active', true)->orderBy('name')->get();
        $primaryUsers = User::whereNull('parent_user_id')
            ->where('is_admin', false)
            ->where('id', '!=', $crm_user->id)
            ->when($crm_user->center_id, fn ($q) => $q->where('center_id', $crm_user->center_id))
            ->orderBy('name')
            ->get();

        return view('admin.crm.users.edit', [
            'user' => $crm_user,
            'centers' => $centers,
            'primaryUsers' => $primaryUsers,
        ]);
    }

    public function update(Request $request, User $crm_user): RedirectResponse
    {
        abort_if($crm_user->isAdmin(), 404);

        $data = $this->validated($request, $crm_user);
        unset($data['photo']);

        if ($request->hasFile('photo')) {
            if ($crm_user->photo_path) {
                Storage::disk('public')->delete($crm_user->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('user-photos', 'public');
            $data['photo_url'] = null;
        } elseif (!empty($data['photo_url']) && $data['photo_url'] !== $crm_user->photo_url) {
            if ($crm_user->photo_path) {
                Storage::disk('public')->delete($crm_user->photo_path);
            }
            $data['photo_path'] = null;
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $crm_user->update($data);

        return redirect()
            ->route('admin.crm-users.show', $crm_user)
            ->with('success', __('messages.crm_user_updated'));
    }

    public function editPassword(User $crm_user): View
    {
        abort_if($crm_user->isAdmin(), 404);

        $crm_user->load(['center', 'parent']);

        return view('admin.crm.users.password', ['user' => $crm_user]);
    }

    public function updatePassword(Request $request, User $crm_user): RedirectResponse
    {
        abort_if($crm_user->isAdmin(), 404);

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $crm_user->forceFill([
            'password' => $validated['password'],
            'remember_token' => null,
        ])->save();

        return redirect()
            ->route('admin.crm-users.show', $crm_user)
            ->with('success', __('messages.password_updated'));
    }

    public function destroy(User $crm_user): RedirectResponse
    {
        abort_if($crm_user->isAdmin(), 404);

        if ($crm_user->photo_path) {
            Storage::disk('public')->delete($crm_user->photo_path);
        }

        $crm_user->delete();

        return redirect()
            ->route('admin.crm-users.index')
            ->with('success', __('messages.crm_user_deleted'));
    }

    private function validated(Request $request, ?User $user): array
    {
        $passwordRules = $user
            ? ['nullable', 'confirmed', Password::defaults()]
            : ['required', 'confirmed', Password::defaults()];

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'password' => $passwordRules,
            'center_id' => 'required|exists:centers,id',
            'parent_user_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(fn ($q) => $q
                    ->whereNull('parent_user_id')
                    ->where('center_id', $request->input('center_id'))),
            ],
            'phone' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'photo_url' => 'nullable|url:http,https|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['parent_user_id'] = $data['parent_user_id'] ?: null;

        return $data;
    }
}
