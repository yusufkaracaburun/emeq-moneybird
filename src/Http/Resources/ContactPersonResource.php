<?php

namespace Emeq\Moneybird\Http\Resources;

class ContactPersonResource extends MoneybirdResource
{
    /**
     * @return array<string, string>
     */
    protected function getFields(): array
    {
        return [
            'id'              => 'id',
            'contact_id'      => 'contact_id',
            'administration_id' => 'administration_id',
            'firstname'       => 'firstname',
            'lastname'        => 'lastname',
            'phone'           => 'phone',
            'email'           => 'email',
            'department'      => 'department',
            'created_at'      => 'created_at',
            'updated_at'      => 'updated_at',
            'version'         => 'version',
        ];
    }
}

