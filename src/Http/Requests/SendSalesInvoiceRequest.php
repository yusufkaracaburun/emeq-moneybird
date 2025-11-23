<?php

namespace Emeq\Moneybird\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Picqer\Financials\Moneybird\Entities\SalesInvoice\SendInvoiceOptions;

class SendSalesInvoiceRequest extends FormRequest
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
            'delivery_method' => [
                'nullable',
                'string',
                'in:'.implode(',', [
                    SendInvoiceOptions::METHOD_EMAIL,
                    SendInvoiceOptions::METHOD_POST,
                    SendInvoiceOptions::METHOD_MANUAL,
                ]),
            ],
        ];
    }

    /**
     * Get validated data with default delivery method.
     *
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        if (! isset($validated['delivery_method'])) {
            $validated['delivery_method'] = SendInvoiceOptions::METHOD_EMAIL;
        }

        return $validated;
    }
}
