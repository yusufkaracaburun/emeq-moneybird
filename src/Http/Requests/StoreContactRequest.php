<?php

namespace Emeq\Moneybird\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
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
            'firstname' => [
                'nullable',
                'string',
                'max:255',
                'required_without:company_name',
            ],
            'lastname' => [
                'nullable',
                'string',
                'max:255',
                'required_without:company_name',
            ],
            'company_name' => [
                'nullable',
                'string',
                'max:255',
                'required_without_all:firstname,lastname',
            ],
            'email'      => ['nullable', 'email', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:255'],
            'address1'   => ['nullable', 'string', 'max:255'],
            'address2'   => ['nullable', 'string', 'max:255'],
            'zipcode'    => ['nullable', 'string', 'max:255'],
            'city'       => ['nullable', 'string', 'max:255'],
            'country'    => ['nullable', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:255'],
        ];
    }
}
