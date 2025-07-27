<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTranslationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'key' => 'required|string|max:255',
            'translations' => 'required|array|min:1',
            'translations.*' => 'required|string|max:1000',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',
        ];
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'key.required' => 'The translation key is required.',
            'key.string' => 'The translation key must be a string.',
            'key.max' => 'The translation key may not be greater than 255 characters.',

            'translations.required' => 'At least one translation is required.',
            'translations.array' => 'Translations must be provided as an array.',
            'translations.*.required' => 'Each translation value is required.',
            'translations.*.string' => 'Each translation must be a string.',
            'translations.*.max' => 'Each translation may not be greater than 1000 characters.',

            'tags.array' => 'Tags must be provided as an array.',
            'tags.*.integer' => 'Each tag must be a valid tag ID (integer).',
            'tags.*.exists' => 'One or more of the selected tags do not exist.',
        ];
    }
}
