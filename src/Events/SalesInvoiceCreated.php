<?php

namespace Emeq\Moneybird\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SalesInvoiceCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public array $payload
    ) {}
}
