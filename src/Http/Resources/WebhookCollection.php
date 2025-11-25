<?php

namespace Emeq\Moneybird\Http\Resources;

class WebhookCollection extends MoneybirdCollection
{
    protected function resourceClass(): string
    {
        return WebhookResource::class;
    }
}
