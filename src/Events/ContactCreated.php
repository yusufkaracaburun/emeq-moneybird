<?php

namespace Emeq\Moneybird\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public array $payload
    ) {}
}
