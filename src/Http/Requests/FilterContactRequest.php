<?php

namespace Emeq\Moneybird\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterContactRequest extends FormRequest
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
            'per_page'         => ['nullable', 'integer', 'min:1', 'max:100'],
            'page'             => ['nullable', 'integer'],
            'query'            => ['nullable', 'string'],
            'include_archived' => ['nullable', 'boolean'],
            'todo'             => ['nullable', 'string'],
            'contact_field'    => ['nullable', 'string'],
            'contact_value'    => ['nullable', 'string'],
        ];
    }
}
