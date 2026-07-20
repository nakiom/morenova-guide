<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pathology_id' => 'required|exists:pathologies,id',
            'order' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'translations' => 'nullable|array',
            'translations.*.locale' => 'required_with:translations.*|string|max:5',
            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
        ];
    }
}
