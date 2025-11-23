<?php

namespace Emeq\Moneybird\Http\Resources;

class ContactResource extends MoneybirdResource
{
    /**
     * Get the field mappings for this resource.
     *
     * @return array<string, string|array<string>>
     */
    protected function getFields(): array
    {
        return [
            'id' => 'id',
            'administration_id' => 'administration_id',
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'company_name' => 'company_name',
            'email' => 'email',
            'phone' => 'phone',
            'address1' => 'address1',
            'address2' => 'address2',
            'zipcode' => 'zipcode',
            'city' => 'city',
            'country' => 'country',
            'tax_number' => 'tax_number',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        ];
    }
}
