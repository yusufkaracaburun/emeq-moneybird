<?php

namespace Emeq\Moneybird\Http\Resources;

class AdministrationCollection extends MoneybirdCollection
{
    protected function resourceClass(): string
    {
        return AdministrationResource::class;
    }
}
