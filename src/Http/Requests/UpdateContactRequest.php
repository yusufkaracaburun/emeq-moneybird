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
            'email'                      => ['sometimes', 'email', 'max:255'],
            'phone'                      => ['sometimes', 'string', 'max:255'],
            'customer_id'                => ['sometimes', 'string', 'max:255'],
            'chamber_of_commerce'        => ['sometimes', 'string', 'max:255'],
            'address1'                   => ['sometimes', 'string', 'max:255'],
            'address2'                   => ['sometimes', 'string', 'max:255'],
            'zipcode'                    => ['sometimes', 'string', 'max:255'],
            'city'                       => ['sometimes', 'string', 'max:255'],
            'country'                    => ['sometimes', 'string', 'max:255'],
            'tax_number'                 => ['sometimes', 'string', 'max:255'],
            'delivery_method'            => ['sometimes', 'string', 'in:Email,Manual'],
            'email_ubl'                  => ['sometimes', 'boolean'],
            'send_invoices_to_attention' => ['sometimes', 'string', 'max:255'],
            'send_invoices_to_email'     => ['sometimes', 'email', 'max:255'],
            'send_estimates_to_attention' => ['sometimes', 'string', 'max:255'],
            'send_estimates_to_email'    => ['sometimes', 'email', 'max:255'],
            'sepa_iban'                  => ['sometimes', 'string', 'max:255'],
            'sepa_iban_account_name'    => ['sometimes', 'string', 'max:70'],
            'sepa_active'                => ['sometimes', 'boolean'],
            'is_trusted'                 => ['sometimes', 'boolean'],
            'invoice_workflow_id'        => ['sometimes', 'integer'],
            'estimate_workflow_id'       => ['sometimes', 'integer'],
        ];
    }
}
