<?php

namespace Emeq\Moneybird\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesInvoiceRequest extends FormRequest
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
            'contact_id'           => ['nullable', 'string'],
            'invoice_id'           => ['nullable', 'string', 'max:255'],
            'state'                => ['nullable', 'string', 'max:255'],
            'invoice_date'         => ['nullable', 'date'],
            'due_date'             => ['nullable', 'date'],
            'total_price_excl_tax' => ['nullable', 'numeric'],
            'total_price_incl_tax' => ['nullable', 'numeric'],
            'currency'             => ['nullable', 'string', 'max:3'],
        ];
    }
}
