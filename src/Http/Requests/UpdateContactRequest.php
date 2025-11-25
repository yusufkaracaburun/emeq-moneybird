<?php

namespace Emeq\Moneybird\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
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
                'sometimes',
                'string',
                'max:255',
                'required_without:company_name',
            ],
            'lastname' => [
                'sometimes',
                'string',
                'max:255',
                'required_without:company_name',
            ],
            'company_name' => [
                'sometimes',
                'string',
                'max:255',
                'required_without_all:firstname,lastname',
            ],
            'email'                       => ['nullable', 'email', 'max:255'],
            'phone'                       => ['nullable', 'string', 'max:255'],
            'customer_id'                 => ['nullable', 'string', 'max:255'],
            'chamber_of_commerce'         => ['nullable', 'string', 'max:255'],
            'address1'                    => ['nullable', 'string', 'max:255'],
            'address2'                    => ['nullable', 'string', 'max:255'],
            'zipcode'                     => ['nullable', 'string', 'max:255'],
            'city'                        => ['nullable', 'string', 'max:255'],
            'country'                     => ['nullable', 'string', 'max:255'],
            'tax_number'                  => ['nullable', 'string', 'max:255'],
            'delivery_method'             => ['nullable', 'string', 'in:Email,Manual'],
            'email_ubl'                   => ['nullable', 'boolean'],
            'send_invoices_to_attention'  => ['nullable', 'string', 'max:255'],
            'send_invoices_to_email'      => ['nullable', 'email', 'max:255'],
            'send_estimates_to_attention' => ['nullable', 'string', 'max:255'],
            'send_estimates_to_email'     => ['nullable', 'email', 'max:255'],
            'sepa_iban'                   => ['nullable', 'string', 'max:255'],
            'sepa_iban_account_name'      => ['nullable', 'string', 'max:70'],
            'sepa_active'                 => ['nullable', 'boolean'],
            'is_trusted'                  => ['nullable', 'boolean'],
            'invoice_workflow_id'         => ['nullable', 'integer'],
            'estimate_workflow_id'        => ['nullable', 'integer'],
        ];
    }
}
