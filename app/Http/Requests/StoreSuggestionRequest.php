<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Support\SuggestionCaptcha;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSuggestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $user = $this->user();

        if ($user) {
            $this->merge([
                'user_name' => $this->filled('user_name') ? $this->input('user_name') : $user->name,
                'user_email' => $user->email,
            ]);

            return;
        }

        $contact = trim((string) $this->input('contact', ''));
        $this->merge([
            'user_email' => $contact !== '' ? $contact : null,
        ]);
    }

    public function rules(): array
    {
        $rules = [
            'step_id' => 'nullable|exists:steps,id',
            'pathology_id' => 'nullable|exists:pathologies,id',
            'user_name' => 'required|string|max:255',
            'user_email' => 'nullable|string|max:255',
            'doctor_name' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ];

        if (! $this->user()) {
            $rules['contact'] = 'nullable|string|max:255';
            $rules['captcha_answer'] = 'required|integer';
            $rules['website'] = 'nullable|string';
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->user()) {
                return;
            }

            if (filled($this->input('website'))) {
                $validator->errors()->add('captcha_answer', __('messages.suggestion_captcha_invalid'));
                SuggestionCaptcha::refresh();

                return;
            }

            if (! SuggestionCaptcha::check($this->input('captcha_answer'))) {
                $validator->errors()->add('captcha_answer', __('messages.suggestion_captcha_invalid'));
                SuggestionCaptcha::refresh();
            }
        });
    }

    /**
     * Datos listos para persistir en suggestions.
     *
     * @return array{step_id:?int,pathology_id:?int,user_name:string,user_email:?string,doctor_name:?string,message:string,treatment:null}
     */
    public function suggestionPayload(): array
    {
        $data = $this->safe()->only([
            'step_id',
            'pathology_id',
            'user_name',
            'user_email',
            'doctor_name',
            'message',
        ]);

        $data['treatment'] = null;

        return $data;
    }
}
