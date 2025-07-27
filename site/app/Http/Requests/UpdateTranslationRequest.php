<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'translations' => ['required', 'array', 'min:1'],
            'translations.*' => ['required', 'string'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'translations.required' => 'Translations data is required.',
            'translations.array' => 'Translations must be an array.',
            'translations.*.required' => 'Each translation content is required.',
            'tags.array' => 'Tags must be an array.',
            'tags.*.integer' => 'Each tag must be an integer.',
            'tags.*.exists' => 'One or more selected tags are invalid.',
        ];
    }
}
