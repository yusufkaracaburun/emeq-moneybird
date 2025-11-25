<?php

namespace Emeq\Moneybird\Http\Resources;

class SalesInvoiceAttachmentResource extends MoneybirdResource
{
    /**
     * @return array<string, string>
     */
    protected function getFields(): array
    {
        return [
            'id'               => 'id',
            'administration_id'=> 'administration_id',
            'attachable_id'    => 'attachable_id',
            'attachable_type'  => 'attachable_type',
            'filename'         => 'filename',
            'content_type'     => 'content_type',
            'size'             => 'size',
            'rotation'         => 'rotation',
            'created_at'       => 'created_at',
            'updated_at'       => 'updated_at',
        ];
    }
}

