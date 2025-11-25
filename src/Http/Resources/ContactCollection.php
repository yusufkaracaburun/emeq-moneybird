<?php

namespace Emeq\Moneybird\Http\Resources;

class ContactCollection extends MoneybirdCollection
{
    protected function resourceClass(): string
    {
        return ContactResource::class;
    }
}
