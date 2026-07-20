<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Center;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CenterController extends Controller
{
    public function index(Request $request): View
    {
        $query = Center::withCount(['users', 'equipment'])->latest();

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%")
                    ->orWhere('contact_name', 'like', "%{$q}%");
            });
        }

        $centers = $query->paginate(20)->withQueryString();

        return view('admin.crm.centers.index', compact('centers'));
    }

    public function create(): View
    {
        return view('admin.crm.centers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $center = Center::create($data);

        return redirect()
            ->route('admin.centers.show', $center)
            ->with('success', __('messages.center_created'));
    }

    public function show(Center $center): View
    {
        $center->load([
            'users' => fn ($q) => $q->with('parent')->orderBy('name'),
            'equipment.user',
        ]);

        return view('admin.crm.centers.show', compact('center'));
    }

    public function edit(Center $center): View
    {
        return view('admin.crm.centers.edit', compact('center'));
    }

    public function update(Request $request, Center $center): RedirectResponse
    {
        $center->update($this->validated($request));

        return redirect()
            ->route('admin.centers.show', $center)
            ->with('success', __('messages.center_updated'));
    }

    public function destroy(Center $center): RedirectResponse
    {
        $center->delete();

        return redirect()
            ->route('admin.centers.index')
            ->with('success', __('messages.center_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'contact_name' => 'nullable|string|max:255',
            'instagram_url' => 'nullable|url:http,https|max:2048',
            'youtube_url' => 'nullable|url:http,https|max:2048',
            'facebook_url' => 'nullable|url:http,https|max:2048',
            'x_url' => 'nullable|url:http,https|max:2048',
            'notes' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]) + [
            'is_active' => $request->boolean('is_active', true),
        ];
    }
}
