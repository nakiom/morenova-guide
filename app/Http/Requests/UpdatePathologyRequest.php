<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePathologyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $pathologyId = $this->route('pathology');
        
        return [
            'category_id' => 'required|exists:categories,id',
            'slug' => ['required', 'string', 'max:255', Rule::unique('pathologies')->ignore($pathologyId)],
            'icon' => 'nullable|string|max:255',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'order' => 'nullable|integer',
            'transducer' => 'nullable|string|max:50',
            'sessions_count' => 'nullable|string|max:30',
            'sessions_frequency' => 'nullable|string|max:60',
            'session_duration_minutes' => 'nullable|integer|min:0|max:600',
            'applications_per_session' => 'nullable|integer|min:0|max:100',
            'shots_per_application' => 'nullable|integer|min:0|max:10000',
            'shots_per_session' => 'nullable|integer|min:0|max:100000',
            'energy_kv' => 'nullable|numeric|min:0|max:99.99',
            'frequency_hz' => 'nullable|numeric|min:0|max:999.99',
            'translations' => 'nullable|array',
            'translations.*.locale' => 'required_with:translations.*|string|max:5',
            'translations.*.name' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
        ];
    }
}
