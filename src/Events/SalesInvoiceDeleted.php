<?php

namespace Emeq\Moneybird\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SalesInvoiceDeleted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public array $payload
    ) {}
}
