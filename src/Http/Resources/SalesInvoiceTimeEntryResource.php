<?php

namespace Emeq\Moneybird\Http\Resources;

class SalesInvoiceTimeEntryResource extends MoneybirdResource
{
    /**
     * @return array<string, string>
     */
    protected function getFields(): array
    {
        return [
            'id'              => 'id',
            'administration_id' => 'administration_id',
            'contact_id'      => 'contact_id',
            'project_id'      => 'project_id',
            'user_id'         => 'user_id',
            'started_at'      => 'started_at',
            'ended_at'        => 'ended_at',
            'description'     => 'description',
            'paused_duration' => 'paused_duration',
            'billable'        => 'billable',
            'created_at'      => 'created_at',
            'updated_at'      => 'updated_at',
        ];
    }
}

