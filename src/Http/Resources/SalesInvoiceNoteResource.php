<?php

namespace Emeq\Moneybird\Http\Resources;

class SalesInvoiceNoteResource extends MoneybirdResource
{
    /**
     * @return array<string, string>
     */
    protected function getFields(): array
    {
        return [
            'id'              => 'id',
            'administration_id' => 'administration_id',
            'entity_id'       => 'entity_id',
            'entity_type'     => 'entity_type',
            'user_id'         => 'user_id',
            'assignee_id'     => 'assignee_id',
            'todo'            => 'todo',
            'note'            => 'note',
            'completed_at'    => 'completed_at',
            'completed_by_id' => 'completed_by_id',
            'todo_type'       => 'todo_type',
            'data'            => 'data',
            'created_at'      => 'created_at',
            'updated_at'      => 'updated_at',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaults(): array
    {
        return [
            'todo' => false,
            'data' => [],
        ];
    }
}

