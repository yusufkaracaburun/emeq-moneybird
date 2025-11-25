<?php

namespace Emeq\Moneybird\Http\Resources;

class SalesInvoiceCollection extends MoneybirdCollection
{
    protected function resourceClass(): string
    {
        return SalesInvoiceResource::class;
    }
}
