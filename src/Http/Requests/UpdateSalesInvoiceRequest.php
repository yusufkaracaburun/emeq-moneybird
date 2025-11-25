<?php

namespace Emeq\Moneybird\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSalesInvoiceRequest extends FormRequest
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
            'contact_id'           => ['sometimes', 'string'],
            'invoice_id'           => ['sometimes', 'string', 'max:255'],
            'state'                => ['sometimes', 'string', 'max:255'],
            'invoice_date'         => ['sometimes', 'date'],
            'due_date'             => ['sometimes', 'date'],
            'total_price_excl_tax' => ['sometimes', 'numeric'],
            'total_price_incl_tax' => ['sometimes', 'numeric'],
            'currency'             => ['sometimes', 'string', 'max:3'],
        ];
    }
}
