<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EquipmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Equipment::with(['center', 'user'])->latest();

        if ($request->filled('center_id')) {
            $query->where('center_id', $request->center_id);
        }

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($builder) use ($q) {
                $builder->where('serial_number', 'like', "%{$q}%")
                    ->orWhere('model', 'like', "%{$q}%");
            });
        }

        $equipment = $query->paginate(20)->withQueryString();
        $centers = Center::orderBy('name')->get();

        return view('admin.crm.equipment.index', compact('equipment', 'centers'));
    }

    public function create(Request $request): View
    {
        $centers = Center::where('is_active', true)->orderBy('name')->get();
        $centerId = $request->get('center_id');
        $users = User::where('is_admin', false)
            ->when($centerId, fn ($q) => $q->where('center_id', $centerId))
            ->orderBy('name')
            ->get();

        return view('admin.crm.equipment.create', compact('centers', 'users', 'centerId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, null);
        $item = Equipment::create($data);

        return redirect()
            ->route('admin.equipment.index', ['center_id' => $item->center_id])
            ->with('success', __('messages.equipment_created'));
    }

    public function edit(Equipment $equipment): View
    {
        $centers = Center::where('is_active', true)->orderBy('name')->get();
        $users = User::where('is_admin', false)
            ->where('center_id', $equipment->center_id)
            ->orderBy('name')
            ->get();

        return view('admin.crm.equipment.edit', compact('equipment', 'centers', 'users'));
    }

    public function update(Request $request, Equipment $equipment): RedirectResponse
    {
        $equipment->update($this->validated($request, $equipment));

        return redirect()
            ->route('admin.equipment.index', ['center_id' => $equipment->center_id])
            ->with('success', __('messages.equipment_updated'));
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        $centerId = $equipment->center_id;
        $equipment->delete();

        return redirect()
            ->route('admin.equipment.index', ['center_id' => $centerId])
            ->with('success', __('messages.equipment_deleted'));
    }

    private function validated(Request $request, ?Equipment $equipment): array
    {
        $data = $request->validate([
            'center_id' => 'required|exists:centers,id',
            'user_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('center_id', $request->input('center_id'))),
            ],
            'serial_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('equipment', 'serial_number')->ignore($equipment?->id),
            ],
            'model' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['user_id'] = $data['user_id'] ?: null;

        return $data;
    }
}
